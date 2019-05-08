import { absCore } from '../absCore/absCore_00003'

export abstract class foldCore extends absCore {

  constructor(mkt: object, initCalc: boolean = true) {
    // console.log(['foldCore constructor'])
    let t = {market : mkt}
    super(t, initCalc)
    this.setData(['version','foldCore'], 'v00001')
  }
  
  protected initDataCore(files): void {
    // console.log(['foldCore initDataCore', files])
    for (let i of Object.keys(files)) { this.dataFiles[i] = files[i]; }
    this.initData()
  }
    

}