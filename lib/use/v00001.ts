// imports
  import * as d3 from 'd3'
  import * as dc from '../dayCount'

  import { foldCore }  from '../foldCore/foldCore_00001'
  import { hyperCore } from '../hyperCore/hyperCore_00001'
  import { solidCore } from '../solidCore/solidCore_00001'
  import { planeCore } from '../planeCore/planeCore_00001'
  import { lineCore }  from '../lineCore/lineCore_00001'
//

export class fold  extends foldCore {                         /* fold   */
  constructor(market: object, initCalc: boolean = true) {
    super(market, initCalc)
    this.setData(['version','fold'], 'v00001')
    // this.dataCopy(parent, this, ['ancestry']) // no ancestry on root class!
    this.setData(['ancestry'], {})
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
    this.dataCopy(parent, this, ['ancestry'])
    this.setData(['ancestry','fold'], parent)
    } 
  
  /* ****************************************************************** */
    
    public initData(): void { this.setData(['envelope'],  this.dataFiles['envelope'] ); }
    protected initCalc(): void {}
    public    addChild(policy: object): solid {
      let t = new solid(policy, this)
      let k = policy['policyID'] == undefined ? 'tempSolidID' : policy['policyID']
      this.children[k] = t
      return t;    }
    public    populateFromData(propogate: boolean = false): void {      
      let t = 0
      for (let i of this.getData(['envelope','services'])) {
        // for now, only add retailElectricity services
        // in future, will have wholesale trades, gas, etc
        if ( i['service'] == 'retailElectricity' ) { 
          let tChild = this.addChild( { policyID: 'retail_' + t } )
          if (propogate) { tChild.populateFromData(propogate) }
        }
        t = t + 1
      }    }
 
  /* end of class */}

export class solid extends solidCore {                        /* solid  */
  constructor(policy: object, parent: hyper, initCalc: boolean = true) {
    super(policy, initCalc)
    this.setData(['version','solid'], 'v00001')
    this.dataCopy(parent, this, ['ancestry'])
    this.setData(['ancestry','hyper'], parent)
    } 
  
  /* ****************************************************************** */
    
    public initData(): void { this.setData(['policy'],  this.dataFiles['policy'] );    }
    protected initCalc(): void {}
    public    addChild(account: object, parent?: solid): plane {
      let t = new plane(account, this)
      this.children[account['accountID']] = t
      return t; }

    public    populateFromData(propogate: boolean): void {
      let q = this.getData(['ancestry','hyper','_data','envelope','services'])[0]// how to know **WHICH** service?

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
    this.dataCopy(parent, this, ['ancestry'])
    this.setData(['ancestry','solid'], parent)
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
      let tenor = this.getData(['ancestry','hyper','_data','envelope','tenor'])
      
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
    this.dataCopy(parent, this, ['ancestry'])
    this.setData(['ancestry','plane'], parent)
    } 
  
  /* ****************************************************************** */
    
    public    initData(): void {}
    protected initCalc(): void {}
    public    addChild(): void { throw new Error("class line cannot have children") }
    public    populateFromData(propogate: boolean): void { /*throw new Error("class line cannot have children")*/ }
  
  /* end of class */}