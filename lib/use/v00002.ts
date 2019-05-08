// imports
  import * as d3 from 'd3'
  import * as dc from '../dayCount'

  import { foldCore }  from '../foldCore/foldCore_00002'
  import { hyperCore } from '../hyperCore/hyperCore_00002'
  import { solidCore } from '../solidCore/solidCore_00002'
  import { planeCore } from '../planeCore/planeCore_00002'
  import { lineCore }  from '../lineCore/lineCore_00002'
//

export class fold  extends foldCore {                         /* fold   */
  constructor(market: object, initCalc: boolean = true) {
    super(market, initCalc)
    this.setData(['version','fold'], 'v00001')
    // no heritage set at root
    }

  /* ****************************************************************** */

    public initData(): void { this.setData(['market'],  this.dataFiles['market'] ); }
    protected initCalc(): void {}
    public    addChild(envelope: object): hyper|void {
      let t = new hyper(envelope, this)
      this.children[envelope['envelopeID']] = t
      return t;
      }
    public    populateFromData(): void { throw new Error('class fold cannot populate from data'); }

  /* end of class */}

export class hyper extends hyperCore {                        /* hyper  */
  constructor(envelope: object, parent: fold, initCalc: boolean = true) {
    super(envelope, initCalc)
    this.setData(['version','fold'], 'v00001')
    this.setHeritage(parent, 'fold')
    } 
  
  /* ****************************************************************** */
    
    public initData(): void { this.setData(['envelope'],  this.dataFiles['envelope'] ); }
    protected initCalc(): void {}
    public    addChild(service: object): solid {
      let t = new solid(service, this)
      let k = service['serviceID'] == undefined ? 'tempSolidID' : service['serviceID']
      this.children[k] = t
      return t;    }
    public    populateFromData(propogate: boolean = false): void {      
      let t = 0
      for (let i of this.getData(['envelope','services'])) {
        // for now, only add retailElectricity services
        // in future, will have wholesale trades, gas, etc
        if ( i['service'] == 'retailElectricity' ) { 
          let tChild = this.addChild( { serviceID: 'retail_' + t } )
          if (propogate) { tChild.populateFromData(propogate) }
        }
        t = t + 1
      }    }
 
  /* end of class */}

export class solid extends solidCore {                        /* solid  */
  constructor(service: object, parent: hyper, initCalc: boolean = true) {
    super(service, initCalc)
    this.setData(['version','solid'], 'v00001')
    this.setHeritage(parent, 'hyper')
    } 
  
  /* ****************************************************************** */
    
    public initData(): void { 
      this.setData(['service'],  this.dataFiles['service'] );
      
      }
    protected initCalc(): void {}
    public    addChild(account: object, parent?: solid): plane {
      let t = new plane(account, this)
      this.children[account['accountID']] = t
      return t; }

    public    populateFromData(propogate: boolean): void {
      let q = this.heritage.hyper.getData(['envelope','services'])[0]

      for(let i of q['accountList']) {
        let filePath = 'https://raw.githubusercontent.com/nonfinity/cloud-spread-02/absCore/account/'
        d3.json(filePath + i + '.json').then((d) => { 
          let tChild = this.addChild(d, this)
          if (propogate) { tChild.populateFromData(propogate) }
          } )  
      } }

  /* end of class */}

export class plane extends planeCore {                        /* plane  */
  constructor(account: object, parent: solid, initCalc: boolean = true) {
    super(account, initCalc)
    this.setData(['version','plane'], 'v00001')
    this.setHeritage(parent, 'solid')
    } 
  
  /* ****************************************************************** */
    
    public initData(): void { this.setData(['account'],  this.dataFiles['account'] ); }
    protected initCalc(): void {}
    public    addChild(term: object): line {
      let t = new line(term, this)
      let tName = d3.utcFormat("%Y-%m-%d")(new Date(Date.UTC(term['start'].getUTCFullYear(), term['start'].getUTCMonth() + 1, 1)))
      this.children[d3.utcFormat("%Y-%m-%d")(term['start'])] = t
      return t; }

    public    populateFromData(propogate: boolean): void { 
      let tenor = this.heritage.hyper.getData(['envelope','tenor'])
      
      let suffix = 'T00:00:00.000Z' // EVERYTHING IS UTC
      let envBeg = new Date(Date.parse(tenor.start + suffix))
      let envEnd: Date
      if (tenor.end.date == '') {
          envEnd = new Date(Date.UTC(envBeg.getUTCFullYear(), envBeg.getUTCMonth() + tenor.end.months, envBeg.getUTCDate() - 1))
      } else {
          envEnd = new Date(Date.parse(tenor.end.date + suffix));
      }

      let tDate = envBeg
      do {
        let tChild = this.addChild( {start: tDate, end: dc.eoMonth(tDate, 0) } )
        if (propogate) { tChild.populateFromData(propogate) }
        
        // increment tDate
        tDate = new Date(Date.UTC(tDate.getUTCFullYear(), tDate.getUTCMonth() + 1))
      } while (tDate < envEnd) }
    

  /* end of class */}

export class line  extends lineCore {                         /* line   */
  constructor(term: object, parent: plane, initCalc: boolean = true) {
    super(term, initCalc)
    this.setData(['version','solid'], 'v00001')
    this.setHeritage(parent, 'plane')
    } 
  
  /* ****************************************************************** */
    
    public    initData(): void {    // AVOID CALLING getData() here. Minimize order dependency.
        this.dataDateTime()         // date and time
        this.dataEnergy();          // market info
        this.dataMarket();
        this.dataBusiness();
        this.dataEnvelope();        // envelope info
        this.dataAccount();         // account data
        this.dataUsage();
        this.dataDemand();

        this.dataPosition();       // fixed output
    }
    protected initCalc(): void {}
    public    addChild(): void { throw new Error("class line cannot have children") }
    public    populateFromData(propogate: boolean): void { /*throw new Error("class line cannot have children")*/ }
  
  // DATA INITIALIZERS
    private dataDateTime() {       // Date and Time
      // set _data['date']
      let bDate = this.getData(['date','begDate'])
      let eDate = this.getData(['date','endDate'])
      let yearMo = new Date(Date.UTC(bDate.getUTCFullYear(), bDate.getUTCMonth(), 1))
      this.setData(['date','yearMo'], yearMo);
      this.setData(['date','moStr'], d3.utcFormat("%b")(yearMo));
      this.setData(['date','dateStr'], d3.utcFormat("%Y-%m-%d")(yearMo));

      if      (bDate >= yearMo && bDate <= dc.eoMonth(yearMo,0)) { this.setData(['date','keyMo'], 'start') }
      else if (eDate >= yearMo && eDate <= dc.eoMonth(yearMo,0)) { this.setData(['date','keyMo'], 'end') }
      else    { this.setData(['date','keyMo'], 'middle') }

      // set _data['time]
      this.setData(['time','months'], 1);
      this.setData(['time','days','atc'],   dc.dayCount(this.getData(['date','begDate']),this.getData(['date','endDate']),'atc',   false));
      this.setData(['time','days','wkday'], dc.dayCount(this.getData(['date','begDate']),this.getData(['date','endDate']),'peak',  false));
      this.setData(['time','days','wkend'], dc.dayCount(this.getData(['date','begDate']),this.getData(['date','endDate']),'wkend', false));

      this.setData(['time','hours','peak'],  dc.dayCount(this.getData(['date','begDate']),this.getData(['date','endDate']),'peak',  true));
      this.setData(['time','hours','wkend'], dc.dayCount(this.getData(['date','begDate']),this.getData(['date','endDate']),'wkend', true));
      this.setData(['time','hours','night'], dc.dayCount(this.getData(['date','begDate']),this.getData(['date','endDate']),'night', true));
      this.setData(['time','hours','atc'],   dc.dayCount(this.getData(['date','begDate']),this.getData(['date','endDate']),'atc',   true));
      }
    
    dataEnergy() {}
    dataMarket() {}
    dataBusiness() {}
    dataEnvelope() {}
    dataAccount() {
      this.setData(['account','state'], 'OH'); // hard code it for the moment
      this.setData(['account','recBuckets'], ['solar','nonSolar']); // hard code it for the moment
      this.setData(['account','lossLevel'], 'secondary'); // hard code it for the moment

      console.log(this) // need to set heritage before initializing data! reorder!
      let dPoint = this.heritage['plane'].getData(['account','deliveryPoint'])
      this.setData(['account','deliveryPoint'],dPoint )
      this.setData(['account','marketSource'], this.heritage['fold'].getData(['market','basis'][dPoint]['source'] ) )
      }
    dataUsage() {}
    dataDemand() {}
    dataPosition() {}
  /* end of class */}