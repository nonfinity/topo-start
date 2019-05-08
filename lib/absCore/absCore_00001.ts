// interface childList {
//   // [index: number] : absCore;
//   [childName: string] : absCore;
// }

interface dataFiles {
  [name: string] : any;
}

export abstract class absCore {
  protected dataFiles = {}
  protected children = {}
  protected _data = {}
  protected _calc = {}
  protected isAbsCore: boolean = true;  // good for identifying any child class members
  //

  constructor(files: dataFiles, initCalc: boolean = true) {
    // console.log(['absdCore constructor'])
    this.setData(['version','absCore'], 'v00001')
    this.initDataCore(files);
    this.constructorCleanup();

    // if (initCalc) { this.initCalcCore(); }
    if (initCalc) { this.initCalc(); }
  }
  
  // private members used for data cleanup
    private constructorCleanup() {
      // console.log(['absdCore DeleteShit', this])
      delete this.dataFiles;
      delete this.setData;  }

  // members which will need implementations in child classes
    protected initDataCore(files): void      { this.initData()  }
    
    // protected initCalcCore(): void      { throw new Error('absCore method initCalcCore() has no implementation!'); }
    protected initData(): void            { throw new Error('absCore method initData() has no implementation!'); }
    protected initCalc(): void            { throw new Error('absCore method initCalc() has no implementation!'); }
    public    addChild(a): absCore|void   { throw new Error('absCore method addChild() has no implementation!'); }
    public    populateFromData(propogate: boolean): void  { throw new Error('absCore method populateFromData() has no implementation!'); }
    

  // implement getters/setters
    protected getCore(location: string, path: string | string[]) {
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
    public    getData(path: string | string[]) { return this.getCore('_data', path) }
    public    getCalc(path: string | string[]) { return this.getCore('_calc', path) }
    protected dataCopy(source: absCore, target: absCore, path: string|string[]): void {
      if (!Array.isArray(path)) { path = [path] }
      let s = source.getData(path)
      let t
      if (!target.checkData(path)) {
             t = target.setData(path, {}) }
      else { t = target.getData(path) } 

      for (let i of Object.keys(s)) {
        try {
          let x = s[i].isAbsCore
          if(x) { 
                  this.dataCopy(source, target, [...path, i] ) }
          else {  target.setData([...path, i], source.getData([...path, i])) }
        } catch(e) {
            // console.log(e)
            target.setData([...path, i], source.getData([...path, i]))
        } 
      } }

    protected setCore(location: string, path: string | string[], value): void {
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
    protected setData(path: string | string[], value): void { this.setCore('_data', path, value) }
    public    setCalc(path: string | string[], value): void { this.setCore('_calc', path, value) }

    protected checkCore(path: string | string[], root: object): boolean {
      if (!Array.isArray(path)) { return path in root} 
      else {
        if (path[0] in root) { 
          if (path.length == 1) { return true } 
          else { return this.checkCore( path.slice(1, path.length), root[path[0]] ) }
        } else { return false }
      } }
    public    checkData(path: string | string[]): boolean { return this.checkCore(path, this._data) }
    public    checkCalc(path: string | string[]): boolean { return this.checkCore(path, this._calc) }

    // Return q[i][field] from array q where q[i] is the greatest value before or equal to dStr
    // it is the excel =LOOKUP() function
    protected dateLookUp(q, dStr: string, field: string) {
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

  // inheritable public functions
    public getChild(key: string) { return this.children[key] }
    public getChildren(): absCore[] {
      let t = [];
      for (let i of Object.keys(this.children)) { t.push(this.children[i]); }
      return t; }
    
    public aggregate(childPath: string[], parentPath: string[] = childPath): void {
      // force children to aggregate first
        for (let i of this.getChildren()) { i.aggregate(childPath, parentPath) }
      
        // make a little shortcut to improve readability later
        let q = this.getChildren()[0].getCalc(childPath)

        // now iterate through each of the elements at the path
        for (let i of Object.keys(q)) {

          if (typeof q[i] == 'object') {  // if the element is an object, iterate down through it
            this.aggregate([...childPath, i],[...parentPath, i])
          } else {                        // otherwise iterate across children to sum it all up
            let tempVal = 0;
            for(let j of this.getChildren()) { tempVal = tempVal + j.getCalc([...childPath, i]) }
            this.setCalc([...parentPath, i], tempVal)
          }
        } }
/**/
}