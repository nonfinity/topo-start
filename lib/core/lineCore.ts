/* ****************************************************************** */
  import * as d3 from 'd3';
  import * as dc from '../dayCount';
  // import { lineCore } from './lineCore'
  // import { planeCore } from './planeCore'


/* ****************************************************************** */
// 🔺 imports and misc
//  
// 🔻 abstract class lineCore
/* ****************************************************************** */

  export class lineCore {
    // declare some shit
      protected market:   object = null;  // market   = curves, ancs, business cost estimates, etc
      protected envelope: object = null;  // envelope = term, products, calcGoal, etc
      protected account:  object = null;  // account  = delivery point, load data, PLCs, etc

      private begDate: Date;
      private endDate: Date;
      private yearMo:  Date;

    
    // CONSTRUCTOR. yay!
      constructor(market: object, envelope: object, account: object, begDate: Date, endDate: Date, yearMo?: Date ) {
        this.market = market;
        this.envelope = envelope;
        this.account = account;
        
        // initial date  assignment
        if (yearMo == undefined) { yearMo = new Date(Date.UTC(begDate.getUTCFullYear(), begDate.getUTCMonth(), 1)) }
        this.begDate = begDate;
        this.endDate = endDate;
        this.yearMo = yearMo;

        this.setData(['date','begDate'], this.begDate < this.yearMo ? this.yearMo : this.begDate);
        this.setData(['date','endDate'], this.endDate > dc.eoMonth(this.yearMo,0) ? dc.eoMonth(this.yearMo,0) : this.endDate);
        this.setData(['date','yearMo'], this.yearMo);
        this.setData(['date','moStr'], d3.utcFormat("%b")(this.yearMo));
        this.setData(['date','dateStr'], d3.utcFormat("%Y-%m-%d")(this.yearMo));

        if      (this.begDate >= this.yearMo && this.begDate <= dc.eoMonth(this.yearMo,0)) { this.setData(['date','keyMo'], 'start') }
        else if (this.endDate >= this.yearMo && this.endDate <= dc.eoMonth(this.yearMo,0)) { this.setData(['date','keyMo'], 'end') }
        else    { this.setData(['date','keyMo'], 'middle') }
        
        this.baseDataPrep(); // call all them loaders
      } 

    // Custom getter/setter and tools needed to read in EAM files
      // (can't use base functionality because here we need to supply path to get/set)
      
      private   _data = {}; // = subset of info from MEA that applies to this column. Meant to be read only
      protected _calc = {}; // = calculated cash flow based on _data. This is read/write for child classes
          
      private   getCore(location: string, path: string | string[]) {
        if (this.checkCore(path, this[location]) ) {
          if (!Array.isArray(path)) { return this[location][path] } 
          else {
            let tempRoot = this[location];
            for(let i in path) {
              if (parseInt(i) == path.length-1) { return tempRoot[path[i]] }
              else { tempRoot = tempRoot[path[i]] }
            }
          }
        } }
      public getData(path: string | string[]) { return this.getCore('_data', path) }
      public getCalc(path: string | string[]) { return this.getCore('_calc', path) }

      // private core definition for setting data within a local object.
      // how to handle when data['item'] = 5 becomes data['item']['sub'] ? error? move to default value like data['item']['orig'] ?
      private   setCore(location: string, path: string | string[], value): void {
        if (!Array.isArray(path)) { this[location][path] = value } 
        else {
          let tempRoot = this[location];
          for(let i in path) {
            // console.log([path, tempRoot, parseInt(i), path.length-1])
            if (parseInt(i) == path.length-1) {
              // console.log([tempRoot, path[i], value ])
              tempRoot[path[i]] = value;
            } else { 
              if (!(path[i] in tempRoot)) { tempRoot[path[i]] = {} }
              tempRoot = tempRoot[path[i]]
            }
          }
        } }
      private   setData(path: string | string[], value): void { this.setCore('_data', path, value) }
      protected setCalc(path: string | string[], value): void { this.setCore('_calc', path, value) }

      private checkCore(path: string | string[], root: object): boolean {
        if (!Array.isArray(path)) { return path in root} 
        else {
          if (path[0] in root) { 
            if (path.length == 1) { return true } 
            else { return this.checkCore( path.slice(1, path.length), root[path[0]] ) }
          } else { return false }
        } }
      public checkData(path: string | string[]): boolean { return this.checkCore(path, this._data) }
      public checkCalc(path: string | string[]): boolean { return this.checkCore(path, this._calc) }
    
      // Return q[i][field] from array q where q[i] is the greatest value before or equal to dStr
      // it is the excel =LOOKUP() function
      private dateLookUp(q, dStr: string, field: string) {
        if (!q) { return -Infinity }
        if (Object.keys(q).length === 0 && q.constructor === Object) { return -Infinity; } // if q is not an array. bail
        if (Object.keys(q).length == 1) { return q[Object.keys(q)[0]][field] }             // if length==1 then don't search
        if (dStr in q) { return q[dStr][field] }          // if a perfect match exists, use it!

        let suffix = 'T00:00:00.000Z' // EVERYTHING IS UTC
        let tmp = Object.keys(q)[0]
        let iDate: Date // iterating date
        let tDate: Date // last best found date
        let dDate: Date = new Date(Date.parse(dStr + suffix)) // searched for date

        // console.log( Object.keys(q) )
        for (let i of Object.keys(q)) {
          iDate = new Date(Date.parse(i + suffix ))
          tDate = new Date(Date.parse(tmp + suffix ))
          // console.log(i)
          // console.log([iDate, tDate, dDate,iDate > tDate && iDate <= dDate])
          if (iDate > tDate && iDate <= dDate) { tmp = i }
        }
        // console.log([q, tmp, field])

        return q[tmp][field]; }

    // Data Loaders
      private baseDataPrep() {
        this.baseDateTime();
        
        // load account
        this.baseAccount();
        this.baseUsage();
        this.baseDemand();

        //Load Market
        this.baseEnergyCurve();
        this.baseMarketCurve();
        this.baseBusinessCurve();

        // Load enevelope
        this.baseEnvelope();
        
        // Positions, etc
        this.basePositions();  }

      private baseAccount() {
        this.setData(['account','state'], 'OH'); // hard code it for the moment
        this.setData(['account','recBuckets'], ['solar','nonSolar']); // hard code it for the moment
        this.setData(['account','lossLevel'], 'secondary'); // hard code it for the moment

        this.setData(['account','deliveryPoint'],this.account['deliveryPoint']);
        this.setData(['account','marketSource'], this.market['basis'][this.getData(['account','deliveryPoint'])]['source']); }

      private baseDateTime():void {       // Date and Time
        this.setData(['time','months'], 1);
        this.setData(['time','days','atc'],   dc.dayCount(this.getData(['date','begDate']),this.getData(['date','endDate']),'atc',   false));
        this.setData(['time','days','wkday'], dc.dayCount(this.getData(['date','begDate']),this.getData(['date','endDate']),'peak',  false));
        this.setData(['time','days','wkend'], dc.dayCount(this.getData(['date','begDate']),this.getData(['date','endDate']),'wkend', false));

        this.setData(['time','hours','peak'],  dc.dayCount(this.getData(['date','begDate']),this.getData(['date','endDate']),'peak',  true));
        this.setData(['time','hours','wkend'], dc.dayCount(this.getData(['date','begDate']),this.getData(['date','endDate']),'wkend', true));
        this.setData(['time','hours','night'], dc.dayCount(this.getData(['date','begDate']),this.getData(['date','endDate']),'night', true));
        this.setData(['time','hours','atc'],   dc.dayCount(this.getData(['date','begDate']),this.getData(['date','endDate']),'atc',   true)); }

      private baseUsage():void {          // Usage (KW, KWh, MW, MWh)
        this.setData(['usage','KWh','peak'], +this.account['profile'][this.getData(['date','moStr'])].peak );
        this.setData(['usage','KWh','wkend'], +this.account['profile'][this.getData(['date','moStr'])].wkend);
        this.setData(['usage','KWh','night'], +this.account['profile'][this.getData(['date','moStr'])].night);
        this.setData(['usage','KWh','atc'], this.getData(['usage','KWh','peak'])
                                          + this.getData(['usage','KWh','wkend']) 
                                          + this.getData(['usage','KWh','night']));

        this.setData(['usage','KW','peak'], this.getData(['usage','KWh','peak'])/this.getData(['time','hours','peak']));
        this.setData(['usage','KW','wkend'], this.getData(['usage','KWh','wkend'])/this.getData(['time','hours','wkend']));
        this.setData(['usage','KW','night'], this.getData(['usage','KWh','night'])/this.getData(['time','hours','night']));
        this.setData(['usage','KW','atc'], this.getData(['usage','KWh','atc'])/this.getData(['time','hours','atc']));

        this.setData(['usage','MW','peak'], this.getData(['usage','KW','peak'])/1000);
        this.setData(['usage','MW','wkend'], this.getData(['usage','KW','wkend'])/1000);
        this.setData(['usage','MW','night'], this.getData(['usage','KW','night'])/1000);
        this.setData(['usage','MW','atc'], this.getData(['usage','KW','atc'])/1000);

        this.setData(['usage','MWh','peak'], this.getData(['usage','KWh','peak'])/1000);
        this.setData(['usage','MWh','wkend'], this.getData(['usage','KWh','wkend'])/1000);
        this.setData(['usage','MWh','night'], this.getData(['usage','KWh','night'])/1000);
        this.setData(['usage','MWh','atc'], this.getData(['usage','KWh','atc'])/1000); }

      private baseDemand():void {         // Demands (PLC, NSPL, etc)
        this.setData(['demand','capacity','KW'], +this.dateLookUp(this.account['capacity'], this.getData(['date','dateStr']), 'quantity' ));
        this.setData(['demand','transmission','KW'], +this.dateLookUp(this.account['transmission'], this.getData(['date','dateStr']), 'quantity' ));
        
        this.setData(['demand','capacity','MW'], this.getData(['demand','capacity','KW'])/1000);
        this.setData(['demand','transmission','MW'], this.getData(['demand','transmission','KW'])/1000); }

      private baseEnergyCurve():void {    // Power curve (market point, basis, hedge cost, composite)

        this.setData(['energy','market','peak'],  this.dateLookUp(this.market['energy'][this.getData(['account','marketSource'])], 
                                                                  this.getData(['date','dateStr']), 'peak') );
        this.setData(['energy','market','wkend'], this.dateLookUp(this.market['energy'][this.getData(['account','marketSource'])], 
                                                                  this.getData(['date','dateStr']), 'wrap') );
        this.setData(['energy','market','night'], this.getData(['energy','market','wkend']) );
        this.setData(['energy','market','atc'],  (this.getData(['energy','market','peak']) * this.getData(['usage','MWh','peak'])
                                                + this.getData(['energy','market','wkend']) * this.getData(['usage','MWh','wkend'])
                                                + this.getData(['energy','market','night']) * this.getData(['usage','MWh','night']))
                                                / this.getData(['usage','MWh','atc']));

        this.setData(['energy','basis','peak'],  +this.market['basis'][this.getData(['account','deliveryPoint'])]
                                                             ['values'][this.getData(['date','moStr'])]['peak'] );
        this.setData(['energy','basis','wkend'], +this.market['basis'][this.getData(['account','deliveryPoint'])]
                                                             ['values'][this.getData(['date','moStr'])]['wrap'] );
        this.setData(['energy','basis','night'], this.getData(['energy','basis','wkend']) );
        this.setData(['energy','basis','atc'],  (this.getData(['energy','basis','peak']) * this.getData(['usage','MWh','peak'])
                                                + this.getData(['energy','basis','wkend']) * this.getData(['usage','MWh','wkend'])
                                                + this.getData(['energy','basis','night']) * this.getData(['usage','MWh','night']))
                                                / this.getData(['usage','MWh','atc']));

        this.setData(['energy','hedge','peak'],  this.market['hedge'][this.getData(['account','marketSource'])]['peak'] );
        this.setData(['energy','hedge','wkend'], this.market['hedge'][this.getData(['account','marketSource'])]['wrap'] );
        this.setData(['energy','hedge','night'], this.getData(['energy','hedge','wkend']) );
        this.setData(['energy','hedge','atc'],  (this.getData(['energy','hedge','peak']) * this.getData(['usage','MWh','peak'])
                                                + this.getData(['energy','hedge','wkend']) * this.getData(['usage','MWh','wkend'])
                                                + this.getData(['energy','hedge','night']) * this.getData(['usage','MWh','night']))
                                                / this.getData(['usage','MWh','atc']));

        this.setData(['energy','composite','peak'],  this.getData(['energy','market','peak'])
                                                   + this.getData(['energy','basis', 'peak'])
                                                   + this.getData(['energy','hedge', 'peak']) );
        this.setData(['energy','composite','wkend'], this.getData(['energy','market','wkend'])
                                                   + this.getData(['energy','basis', 'wkend'])
                                                   + this.getData(['energy','hedge', 'wkend']) );
        this.setData(['energy','composite','night'], this.getData(['energy','market','night'])
                                                   + this.getData(['energy','basis', 'night'])
                                                   + this.getData(['energy','hedge', 'night']) );
        this.setData(['energy','composite','atc'],   this.getData(['energy','market','atc'])
                                                   + this.getData(['energy','basis', 'atc'])
                                                   + this.getData(['energy','hedge', 'atc']) ); }
      
      private baseMarketCurve():void {    // Other market costs (ancs, ARRs, capacity, etc)

        this.setData(['market','capacity'],     this.dateLookUp(this.market['capacity'][this.getData(['account','deliveryPoint'])]
                                                              , this.getData(['date','dateStr']), 'rate'));
        this.setData(['market','transmission'], this.dateLookUp(this.market['transmission'][this.getData(['account','deliveryPoint'])]
                                                              , this.getData(['date','dateStr']), 'rate'));
        this.setData(['market','arr'],          this.dateLookUp(this.market['arr'][this.getData(['account','deliveryPoint'])]
                                                              , this.getData(['date','dateStr']), 'rate'));
        this.setData(['market','usageAnc'],     this.dateLookUp(this.market['ancillaries'][this.getData(['account','deliveryPoint'])]
                                                              , this.getData(['date','dateStr']), 'usage'));
        this.setData(['market','demandAnc'],    this.dateLookUp(this.market['ancillaries'][this.getData(['account','deliveryPoint'])]
                                                              , this.getData(['date','dateStr']), 'demand'));
        let r = 0;
        let p = 0;
        let t = this.market['rec'][this.getData(['account','state'])]
        for (let i of this.getData(['account','recBuckets']))
        {
          this.setData(['market','rec',i,'obligationPct'],this.dateLookUp(t['obligationPct'], this.getData(['date','dateStr']), i))
          this.setData(['market','rec',i,'marketPrice'],this.dateLookUp(t['marketPrice'], this.getData(['date','dateStr']), i))
          this.setData(['market','rec',i,'rate'], this.getData(['market','rec',i,'obligationPct']) * this.getData(['market','rec',i,'marketPrice']) )
          
          r = r + this.getData(['market','rec',i,'rate'])
          p = p + this.getData(['market','rec',i,'obligationPct'])
        }
        this.setData(['market','rec','composite','rate'], r)
        this.setData(['market','rec','composite','pct'], p) }
      
      private baseBusinessCurve():void {  // Load business curve into _data

        this.setData(['business','losses'], this.market['losses'][this.getData(['account','deliveryPoint'])][this.getData(['account','lossLevel'])]);
        this.setData(['business','revTax'], this.market['business']['revenueTaxes'][this.getData(['account','state'])]);
        this.setData(['business','WACC'], this.market['business']['WACC']);
        this.setData(['business','riskAdder'], this.market['business']['riskAdder']);
        this.setData(['business','perAccount','startUp'], this.market['business']['perAccountCost']['startUp']);
        this.setData(['business','perAccount','monthly'], this.market['business']['perAccountCost']['monthly']); }


      private basePositions(): void {     // just about there, may need some tweaking
        this.setData(['position','energy','load','peak'],  this.getData(['usage','MWh','peak'])  );
        this.setData(['position','energy','load','wkend'], this.getData(['usage','MWh','wkend']) );
        this.setData(['position','energy','load','night'], this.getData(['usage','MWh','night']) );

        this.setData(['position','energy','loss','peak'],  this.getData(['business','losses']) * this.getData(['usage','MWh','peak'])  );
        this.setData(['position','energy','loss','wkend'], this.getData(['business','losses']) * this.getData(['usage','MWh','wkend']) );
        this.setData(['position','energy','loss','night'], this.getData(['business','losses']) * this.getData(['usage','MWh','night']) );

        this.setData(['position','energy','scalar','peak'],  0);
        this.setData(['position','energy','scalar','wkend'], 0);
        this.setData(['position','energy','scalar','night'], 0);

        this.setData(['position','capacity'],     this.getData(['demand','capacity','MW']) );
        this.setData(['position','transmission'], this.getData(['demand','transmission','MW']) );

        for (let i of this.getData(['account','recBuckets'])) {
          this.setData(['position','rec', i], this.getData(['market','rec',i,'obligationPct']) * this.getData(['usage','MWh','atc']) );  
        } }
    
      private baseEnvelope(): void {      // DOES NOT WORK AS ADVERTISED
        this.setData(['envelope','commission','external'], 2 )
        this.setData(['envelope','minMargin'], 3.5 )
      }
    // Recalculate
      public recalculate(): void {}
      public aggregate(): void {}

      // Propogators (goalseek, etc) & recalculator
      // public pushRevRate(newRate: number): void {
      //   this.setCalc(['pice','energy'], newRate);
      //   this.recalculate()
      // }
  }

/* ****************************************************************** */
// 🔺 abstract class columnCore
//  