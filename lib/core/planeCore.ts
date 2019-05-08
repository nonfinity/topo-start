/* ****************************************************************** */
  import * as d3 from 'd3';
  import * as dc from '../dayCount';
  import { lineCore } from './lineCore';
  // import { planeCore } from './planeCore';

  import { line } from '../line'; // how to make this arrangement more manageable?

/* ****************************************************************** */
// 🔺 imports and misc
//  
// 🔻 abstract class lineCore
/* ****************************************************************** */

  export class planeCore {
    // declare some shit
      protected market:   object = null;  // market   = curves, ancs, business cost estimates, etc
      protected envelope: object = null;  // envelope = term, products, calcGoal, etc
      protected account:  object = null;  // account  = delivery point, load data, PLCs, etc
      protected lines = [];               // array of lines in this plane
    
    // CONSTRUCTOR. yay!
      constructor(market: object, envelope: object, account: object) {
        this.market = market;
        this.envelope = envelope;
        this.account = account; }

    // Building up a plane
      populateFromEnvelope(): void {
        let p = this.envelope['tenor'];
        let suffix = 'T00:00:00.000Z';
        let bDate = new Date(Date.parse(p['start'] + suffix));
        let eDate: Date;
        if (p['end']['date'] == '') {
          eDate = new Date(Date.UTC(bDate.getUTCFullYear(), bDate.getUTCMonth() + p['end']['months'], bDate.getUTCDate() - 1))
        } else {
          eDate = new Date(Date.parse(p['end']['date'] + suffix));
        }

        this.addLinesBetween(bDate, eDate); }

      addLine(begDate: Date, endDate: Date): void {
        this.lines.push(new line(this.market, this.envelope, this.account, begDate, endDate) ) }

      addLinesBetween(begDate: Date, endDate: Date): void {
        let tDate = new Date(begDate.getTime())
        do {
          this.addLine(tDate, dc.eoMonth(tDate,0))
          tDate = new Date(Date.UTC(tDate.getUTCFullYear(), tDate.getUTCMonth() + 1))
        } while (tDate < endDate) }
    
    // Custom getter/setter and tools needed to read in EAM files
      // (can't use base functionality because here we need to supply path to get/set)
      
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
      protected setCalc(path: string | string[], value): void { this.setCore('_calc', path, value) }

      private checkCore(path: string | string[], root: object): boolean {
        if (!Array.isArray(path)) { return path in root} 
        else {
          if (path[0] in root) { 
            if (path.length == 1) { return true } 
            else { return this.checkCore( path.slice(1, path.length), root[path[0]] ) }
          } else { return false }
        } }
      public checkCalc(path: string | string[]): boolean { return this.checkCore(path, this._calc) }

    // Accessing / retreiving members
      public cross(path: string | string[], rowName: string | null = null) {   // update to return row title as well
        if (this.lines.length == 0) { return []; }
        if (this.lines[0].checkCalc(path) ) { // if the cross is found in the _calc object
          let t = [];
          t.push( (rowName == null) ? [...path].join('.') : rowName )
          for(let i of this.lines) { t.push(i.getCalc([...path])) }
          return t
        } else if (this.lines[0].checkData([...path])) { // if the cross is found in the _data object
          let t = [];
          t.push( (rowName == null) ? [...path].join('.') : rowName )
          for(let i of this.lines) { t.push(i.getData(path)) }
          return t
        } else { return [] }
      }
    
    // Outputs
      public crossToTable(trID: string, path: string | string[], rowName: string = null) {
        let tr = d3.select('#'  + trID)
        let td = tr.selectAll("td").data(this.cross(path, rowName))
            td.enter()
              .append("td").text((d) => { return d })
            td.exit().remove() }
    
    // Summarize Totals
      public aggregate(childPath: string[], parentPath: string[] = childPath) {
        // force children to aggregate first
        for (let i of this.lines) { i.aggregate(childPath, parentPath) }
        // ok. now proceed
        
        let q = this.lines[0].getCalc(childPath)
        // now iterate through each of the elements at the path
        for (let i of Object.keys(q)) {

          if (typeof q[i] == 'object') {  // if the element is an object, iterate down through it
            this.aggregate([...childPath, i],[...parentPath, i])
          } else {                        // otherwise iterate across children to sum it all up
            let tempVal = 0;
            for(let j of this.lines) { tempVal = tempVal + j.getCalc([...childPath, i]) }
            this.setCalc([...parentPath, i], tempVal)
          }
        } }

    // Propogators (goalseek, etc) & recalculator
      public pushRevRate(newRate: number): void {
        for (let i of this.lines) { i.pushRevRate(newRate) }
        this.aggregate(['total']);
      }
  }