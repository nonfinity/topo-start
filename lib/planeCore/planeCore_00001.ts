import { absCore } from '../absCore/absCore_00001'

export abstract class planeCore extends absCore {

  constructor(act: object, initCalc: boolean = true) {
    // console.log(['foldCore constructor'])
    let t = {account : act}
    super(t, initCalc)
    this.setData(['version','planeCore'], 'v00001')
    }
  
  protected initDataCore(files): void {
    // console.log(['foldCore initDataCore', files])
    for (let i of Object.keys(files)) { this.dataFiles[i] = files[i]; }
    this.initData()
    }

}