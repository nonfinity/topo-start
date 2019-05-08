// VERSION NUMBER
let vNum = 'v000010'
// VERSION NUMBER

import * as d3 from 'd3'
import { absCore } from '../absCore/absCore_00004'
import { objSet }  from '../absCore/absCore_00004'

export abstract class foldCore extends absCore {

  constructor(dataItems: objSet, heritageItems: objSet, ) {    
    dataItems['version']['foldCore'] = vNum
    super(dataItems, heritageItems)
  }

  public addChild(filePath: string): absCore|void {
      d3.json(filePath).then((d) => { this.processChildren(d) })
    }

  abstract processChildren(d);
}