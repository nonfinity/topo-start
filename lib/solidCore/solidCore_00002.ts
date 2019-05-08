import { absCore } from '../absCore/absCore_00003'

export abstract class solidCore extends absCore {

  constructor(svc: object, initCalc: boolean = true/* params tbd */) {
    // console.log(['foldCore constructor'])
    let t = {service : svc}
    super(t, initCalc)
    this.setData(['version','solidCore'], 'v00001')
    }
  
  protected initDataCore(files): void {
    // console.log(['foldCore initDataCore', files])
    for (let i of Object.keys(files)) { this.dataFiles[i] = files[i]; }
    this.initData()
    }

}