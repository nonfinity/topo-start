import { absCore } from '../absCore/absCore_00001'
interface terms {
  start: Date;
  end: Date; }

export abstract class lineCore extends absCore {

  constructor(term: terms, initCalc: boolean = true/* params tbd */) {
    // add files to this.dataFiles from parameters
    super(term, initCalc)
    
    this.setData(['version','lineCore'], 'v00001')
    delete this.children;
    }
  
  protected initDataCore(term): void {
    this.setData(['date','begDate'], term.start)
    this.setData(['date','endDate'], term.end)

    this.initData()
    }

}