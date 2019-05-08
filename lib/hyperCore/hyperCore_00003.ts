// VERSION NUMBER
let vNum = 'v000003'
// VERSION NUMBER

import { absCore } from '../absCore/absCore_00004'
import { objSet }  from '../absCore/absCore_00004'

export abstract class hyperCore extends absCore {

  constructor(dataItems: objSet, heritageItems: objSet) {    
    dataItems['version']['hyperCore'] = vNum
    super(dataItems, heritageItems)
  }
}