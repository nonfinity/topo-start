import { absCore } from '../absCore/absCore_00001'

export abstract class hyperCore extends absCore {

  constructor(env: object, initCalc: boolean = true) {
    // console.log(['foldCore constructor'])
    let t = {envelope : env}
    super(t, initCalc)
    this.setData(['version','hyperCore'], 'v00001')
  }
  
    protected initDataCore(files): void {
    // console.log(['foldCore initDataCore', files])
    for (let i of Object.keys(files)) { this.dataFiles[i] = files[i]; }
    this.initData()
  }

}