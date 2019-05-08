// VERSION NUMBER
let vNum = 'v000003'
// VERSION NUMBER

// imports
  import * as d3 from 'd3'
  import * as dc from '../dayCount'

  import { foldCore }  from '../foldCore/foldCore_00003'
  import { hyperCore } from '../hyperCore/hyperCore_00003'
  import { solidCore } from '../solidCore/solidCore_00003'
  import { planeCore } from '../planeCore/planeCore_00003'
  import { lineCore }  from '../lineCore/lineCore_00003'
//

export class fold  extends foldCore {                         /* fold   */
  constructor(market: object) {
    let heritageItems = {}
    
    let dataItems = {}
    dataItems['market'] = market
    dataItems['version'] = {} // gotta make it an object before you can add properties
    dataItems['version']['fold'] = vNum
    super(dataItems, heritageItems)
    }

  /* ****************************************************************** */

    public    initData(): void {}
    protected initCalc(): void {}
    public    addChild(envelope: object): hyper|void {
      let t = new hyper(envelope, this)
      this.children[envelope['envelopeID']] = t
      return t;
      }
    public    populateFromData(): void { throw new Error('class fold cannot populate from data'); }

  /* end of class */}

export class hyper extends hyperCore {                        /* hyper  */
  constructor(envelope: object, parent: fold) {
    let heritageItems = { parentObj: parent, parentName: 'fold' }
    
    let dataItems = {}
    dataItems['envelope'] = envelope
    dataItems['version'] = {} // gotta make it an object before you can add properties
    dataItems['version']['hyper'] = vNum

    super(dataItems, heritageItems)
    } 
  
  /* ****************************************************************** */
    
    public initData(): void {}
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
  constructor(service: object, parent: hyper) {
    let heritageItems = { parentObj: parent, parentName: 'hyper' }
    
    let dataItems = {}
    dataItems['service'] = service
    dataItems['version'] = {} // gotta make it an object before you can add properties
    dataItems['version']['solid'] = vNum

    super(dataItems, heritageItems)
    } 
  
  /* ****************************************************************** */
    
    public initData(): void {}
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
  constructor(account: object, parent: solid) {
    let heritageItems = { parentObj: parent, parentName: 'solid' }
    
    let dataItems = {}
    dataItems['account'] = account
    dataItems['version'] = {} // gotta make it an object before you can add properties
    dataItems['version']['plane'] = vNum

    super(dataItems, heritageItems)
    }  
  
  /* ****************************************************************** */
    
    public initData(): void {}
    protected initCalc(): void {}
    public    addChild(bDate: Date, eDate: Date): line {
      let t = new line(bDate, eDate, this)
      let tName = d3.utcFormat("%Y-%m-%d")(new Date(Date.UTC(bDate.getUTCFullYear(), bDate.getUTCMonth() + 1, 1)))
      this.children[tName] = t
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
        let tChild = this.addChild( tDate, dc.eoMonth(tDate, 0) )
        if (propogate) { tChild.populateFromData(propogate) }
        
        // increment tDate
        tDate = new Date(Date.UTC(tDate.getUTCFullYear(), tDate.getUTCMonth() + 1))
      } while (tDate < envEnd) }
    

  /* end of class */}

export class line  extends lineCore {                         /* line   */
  constructor(bDate: Date, eDate: Date, parent: plane) {
    let heritageItems = { parentObj: parent, parentName: 'plane' }
    
    let dataItems = {}
    dataItems['date'] = {}    // gotta make it an object before you can add properties
    dataItems['date']['begDate'] = bDate
    dataItems['date']['endDate'] = eDate
    dataItems['version'] = {} // gotta make it an object before you can add properties
    dataItems['version']['line'] = vNum

    super(dataItems, heritageItems)
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
      this.setData(['time','days','atc'],   dc.dayCount(bDate,eDate,'atc',   false));
      this.setData(['time','days','wkday'], dc.dayCount(bDate,eDate,'peak',  false));
      this.setData(['time','days','wkend'], dc.dayCount(bDate,eDate,'wkend', false));

      this.setData(['time','hours','peak'],  dc.dayCount(bDate,eDate,'peak',  true));
      this.setData(['time','hours','wkend'], dc.dayCount(bDate,eDate,'wkend', true));
      this.setData(['time','hours','night'], dc.dayCount(bDate,eDate,'night', true));
      this.setData(['time','hours','atc'],   dc.dayCount(bDate,eDate,'atc',   true));
      }
    
    dataEnergy() {}
    dataMarket() {}
    dataBusiness() {}
    dataEnvelope() {}
    dataAccount() {
      let act = this.heritage.plane.getData(['account']);
      let svc = this.heritage.solid.getData(['service']);
      let mkt = this.heritage.fold.getData(['market']);
      
      // console.log(this) // need to set heritage before initializing data! reorder!
      this.setData(['account','deliveryPoint'], act['deliveryPoint'])
      this.setData(['account','marketSource'], mkt['basis'][ act['deliveryPoint'] ]['source'])
      
      this.setData(['account','state'], act['state']);
      
      console.log(svc)
      this.setData(['account','lossLevel'], svc['lossMap'][ act['deliveryPoint'] ][ act['rateCode'] ]);
      this.setData(['account','recBuckets'], svc['recBuckets'][ act['state'] ]);

      }
    dataUsage() {}
    dataDemand() {}
    dataPosition() {}
  /* end of class */}