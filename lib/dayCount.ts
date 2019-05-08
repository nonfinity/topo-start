const DAY_in_MS: number = 1000 * 60 * 60 * 24 // milliseconds in a day

// WHAT FUCKING STUPID NONSENSE MADE THE MONTHS ZERO BASED
export const holidayDefs = {
  NERC: {
    NewYears: function (d) { return new Date(Date.UTC(d, 0, 1)) },
    MemorialDay: function (d) {
      let t = new Date(Date.UTC(d, 4, 31))
      let j = (14 + t.getUTCDay() - 1) % 7  // add 14 to prevent negatives
      let m = t.getTime() - (j * DAY_in_MS)
      return new Date(m)
    },
    July4th: function (d) { return new Date(Date.UTC(d, 6, 4)) },
    LaborDay: function (d) {
      let t = new Date(Date.UTC(d, 8, 1))
      let j = (14 + t.getUTCDay() - 2) % 7  // add 14 to prevent negatives
      let m = t.getTime() + (6 - j) * DAY_in_MS
      return new Date(m)
    },
    Thanksgiving: function (d) {
      let t = new Date(Date.UTC(d, 10, 1))
      let j = (14 + t.getUTCDay() - 5) % 7  // add 14 to prevent negatives
      let m = t.getTime() + (27 - j) * DAY_in_MS + (DAY_in_MS / 24)
      return new Date(m)
    },
    Christmas: function (d) { return new Date(Date.UTC(d, 11, 25)) }
  },
  DST: {
    Spring: function(d) {
      if (d < 2007) {
        let t = new Date(Date.UTC(d, 3,1))
        let j = (14 + t.getUTCDay() - 1) % 7 + 1
        let m = t.getTime() + (7 - j) * DAY_in_MS
        return new Date(m)
      } else {
        let t = new Date(Date.UTC(d, 2,1))
        let j = (14 + t.getUTCDay() - 1) % 7 + 1
        let m = t.getTime() + (14 - j) * DAY_in_MS
        return new Date(m)
      }
    },
    Fall: function(d) { 
      if (d < 2007) {
        let t = new Date(Date.UTC(d, 9,31))
        let j = (14 + t.getUTCDay() - 0) % 7
        let m = t.getTime() - j * DAY_in_MS
        return new Date(m)
      } else {
        let t = new Date(Date.UTC(d, 10,1))
        let j = (14 + t.getUTCDay() - 1) % 7 + 1
        let m = t.getTime() + (7 - j) * DAY_in_MS
        return new Date(m)
      }
    }
  }
}

export function dayCount(begDate: Date, endDate: Date, bucket: string, getHours: boolean = true): number {
  let okBuckets = ['7x24','5x16','2x16','7x16','5x8','2x8','7x8','atc','peak','wkend','night','sat','sun','wrap'];
  let dstSafe =   [       '5x16','2x16','7x16','5x8'                  ,'peak','wkend'        ,'sat','sun']
    // these buckets are not DST vulnerable
      
  // set everything to lowercase locally to avoid confusion
      okBuckets = okBuckets.join('|').toLocaleLowerCase().split('|')
  let adjBucket = bucket.toLocaleLowerCase();

  if ( okBuckets.indexOf(adjBucket) == -1 ) { return -1; } else { // not in list: == -1 ... in list: !== -1
    if (getHours) { // TRUE = return a count of hours
      if( dstSafe.indexOf(adjBucket) == -1 ) { // getHours = TRUE and bucket is not DST safe
        switch(adjBucket) {
          case '7x24':
          case 'atc':
            return dayCount(begDate, endDate, '7x8', true) + dayCount(begDate, endDate, '7x16', true);
            break;
          
          case '7x8':
          case 'night':
            return dayCount(begDate, endDate, '5x8', true) + dayCount(begDate, endDate, '2x8', true);
            break;

          case '2x8':
            let b = dayCount(begDate, endDate, adjBucket, false) * 8 // hours is DST did not exist
            let dsts = [[],[]] // 0 = Spring, 1 = Fall
            
            let tWeN: Date;
            for (let i = begDate.getFullYear(); i <= endDate.getFullYear(); i++) {
              dsts[0].push(holidayDefs.DST.Spring(i))
              dsts[1].push(holidayDefs.DST.Fall(i))              
            }
            let dCnt = []
                dCnt[0] = dsts[0].filter((e,i,a) => { return (e >= begDate && e <= endDate) }).length
                dCnt[1] = dsts[1].filter((e,i,a) => { return (e >= begDate && e <= endDate) }).length

            return b - dCnt[0] + dCnt[1]
            break;

          case 'wrap':
            return dayCount(begDate, endDate, '7x8', true) + dayCount(begDate, endDate, '2x16', true);
            break;
        }
      } else {  // getHours = TRUE and bucket is DST safe
        let mult = 0;
        switch (adjBucket) {
          case 'atc': case '7x24':
            mult = 24;
            break;

          case '5x16': case '7x16': case 'peak': case '2x16': case 'wkend': case 'sat': case 'sun':
            mult = 16;
            break;

          case '5x8': case '2x8': case 'night':
            mult = 8;
            break;
        }

        return mult * dayCount(begDate, endDate, bucket, false);
      }
    } else {        // FALSE = return a count of days
      //console.log(adjBucket == 'a')
      switch (adjBucket) {
        case '7x24':
        case 'atc':
        case '7x8':
        case 'night':
        case '7x16':
          return (endDate.getTime() - begDate.getTime()) / DAY_in_MS + 1;
          break;
        
        case '5x16':
        case 'peak':
        case '5x8':
          return dayCount(begDate, endDate, '7x24', false) - dayCount(begDate, endDate, '2x16', false);
          break;

        case '2x16':
        case 'wkend':
        case '2x8':
          return dayCount(begDate, endDate, 'sat', false) + dayCount(begDate, endDate, 'sun', false);
          break;

        case 'sat':
          let wSat = dayCount(begDate, endDate, '7x24', false)
          let jSat = (14 + begDate.getUTCDay() + 0) % 7 + 1
          let fSat = jSat + (wSat - Math.floor(wSat/7) * 7 -1) >= 7 ? 1 : 0
          
          return Math.floor(wSat/7) + fSat;
          break;

        case 'sun':
          let wSun = dayCount(begDate, endDate, '7x24', false)
          let jSun = (14 + begDate.getUTCDay() - 1) % 7 + 1
          let fSun = jSun + (wSun - Math.floor(wSun/7) * 7 - 1) >= 7 ? 1 : 0

          let hols = []
          let tHol: Date;
          for (let i = begDate.getFullYear(); i <= endDate.getFullYear(); i++) {
            for (let j in holidayDefs.NERC) {
              tHol = holidayDefs.NERC[j](i);
              if (tHol.getUTCDay() !==  6 ) { hols.push(tHol) }
            }
          }
          let hSun = hols.filter((e,i,a) => { return (e >= begDate && e <= endDate) }).length

          return Math.floor(wSun/7) + fSun + hSun;
          break;

        case 'wrap':
          return -1;
          break;
      }
    }
  }
}

export function eoMonth(begDate: Date, monthAdj: number): Date {
  let y = begDate.getUTCFullYear();
  let m = begDate.getUTCMonth();
  
  let jsMonthWonk = 1; // need to add to becaus JS gets confused with month numbers. Sad!
                       // if you read the above comment in the future and understand why it is needed
                       // just understand that I don't care. Its stupid either way.
  
  let s = new Date(Date.UTC(y,m + jsMonthWonk + monthAdj)).getTime() - DAY_in_MS
  return new Date(s)
}