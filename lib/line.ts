/* ****************************************************************** */
  import * as d3 from 'd3';
  import { lineCore } from './core/lineCore';
  // import * as lc from './core/lineCore';

  let mpTest = true;

/* ****************************************************************** */
// 🔺 imports and misc
//  
// 🔻 implementation class `line` declaration
/* ****************************************************************** */

  export class line extends lineCore {
    constructor(market: object, envelope: object, account: object, begDate: Date, endDate: Date, yearMo?: Date ) {
      super(market, envelope, account, begDate, endDate, yearMo) // call parent constructor: reads files into _data
      this.baseCalcPrep(); }

    private totalizer(path: string | string[], exclude: string | string[] = '' ): number {
      let t = 0; // temporary accumulator
      let q = this.getCalc([...path])
      
      let x: string[]
      if (exclude == '') { x = ['total'] }
      else { x = !Array.isArray(exclude) ? ['total', exclude] : ['total', ...exclude] }

      for (let i of Object.keys(q)) {
        if (typeof q[i] != 'object' && x.indexOf(i) < 0 ) { t = t + q[i] }
      }
      return t; }
    
    // main list of calculations to prep
    private baseCalcPrep() {
      this.prepRevenue();
      this.prepEnergy();
      this.prepMarket();
      this.prepBusiness();
      this.prepTotal();
    }

    // Prep functions for first run     // SOME OF THESE NEED UPDATING TO PULL FROM ENVELOPE
      private prepRevenue(): void  {    // Revenue        (+ is cash in)
        this.setCalc(['price','energy','atc'], 50);
        this.setCalc(['price','demand','PLC'], 0);
        this.setCalc(['price','flat'], 0);

        this.setCalc(['revenue','energy'],   this.getCalc(['price','energy','atc']) * this.getData(['usage','MWh','atc']) ); 
        this.setCalc(['revenue','demand'],   this.getCalc(['price','demand','PLC']) * this.getData(['demand','capacity','MW']) ); 
        this.setCalc(['revenue','flat'],     this.getCalc(['price','flat'])); 
        this.setCalc(['revenue','variable'], 0); 
        this.setCalc(['revenue','total'], this.totalizer(['revenue'])); }

      private prepEnergy(): void   {    // Energy Costs   (+ is cash out)
        // come back and figure out how best to do energy cost buildup (ATC, buckets, seasons, scalars, etc)
        this.setCalc(['energyCost','peak'],  this.getData(['energy','composite','peak'])  * this.getData(['usage','MWh','peak'])  );
        this.setCalc(['energyCost','wkend'], this.getData(['energy','composite','wkend']) * this.getData(['usage','MWh','wkend']) );
        this.setCalc(['energyCost','night'], this.getData(['energy','composite','night']) * this.getData(['usage','MWh','night']) );
        this.setCalc(['energyCost','total'], this.totalizer(['energyCost']) ); }
      
      private prepMarket(): void   {    // Market Costs   (+ is cash out)
        this.setCalc(['marketCost','transmission'], this.getData(['demand','transmission','MW']) * this.getData(['market','transmission']) * this.getData(['time','months']));
        this.setCalc(['marketCost','capacity'],     this.getData(['demand','capacity','MW']) * this.getData(['market','capacity'])  * this.getData(['time','days','atc']) );
        this.setCalc(['marketCost','arr'],          this.getData(['demand','capacity','MW']) * this.getData(['market','arr'])       * this.getData(['time','days','atc']) );
        this.setCalc(['marketCost','demandAnc'],    this.getData(['demand','capacity','MW']) * this.getData(['market','demandAnc']) * this.getData(['time','days','atc']) );
        this.setCalc(['marketCost','usageAnc'],     this.getData(['usage','MWh','atc']) * this.getData(['market','usageAnc']) );
        this.setCalc(['marketCost','rec'],          this.getData(['usage','MWh','atc']) * this.getData(['market','rec','composite','rate']) );
        this.setCalc(['marketCost','total'], this.totalizer(['marketCost']) ); }
      
      private prepBusiness(): void {    // Business Costs (+ is cash out)
        let lossApplicableCosts = [ ['energyCost','total'],
                                    ['marketCost','usageAnc'],
                                    ['marketCost','demandAnc'] ]
        let t = 0 // temp value holder
        for (let i of lossApplicableCosts) { t = t + this.getCalc([...i]) }
        this.setCalc(['businessCost','losses'],     t * this.getData(['business','losses']) );
        
        t = this.getData(['business','perAccount','monthly']) 
        if (this.getData(['date','keyMo']) == 'start') { t = t + this.getData(['business','perAccount','startUp']) }
        this.setCalc(['businessCost','billing'],    t );
        
        this.setCalc(['businessCost','commission'], this.getData(['envelope','commission','external']) * this.getData(['usage','MWh','atc']) );
        this.setCalc(['businessCost','revenueTax'], this.getData(['business','revTax']) * this.getCalc(['revenue','total']) );
        this.setCalc(['businessCost','liquidity'],  0);
        this.setCalc(['businessCost','porFee'],     0);
        this.setCalc(['businessCost','total'], this.totalizer(['businessCost']) ); }
      
      private prepTotal(): void    {    // Net totals     (+ is cash in)
        this.setCalc(['total','revenue'],      this.getCalc(['revenue','total']) );
        this.setCalc(['total','energyCost'],   this.getCalc(['energyCost','total']) * -1 );
        this.setCalc(['total','marketCost'],   this.getCalc(['marketCost','total']) * -1);
        this.setCalc(['total','businessCost'], this.getCalc(['businessCost','total']) * -1);
        this.setCalc(['total','grossMargin'],  this.totalizer(['total'],'grossMargin') );    
        
        // put this below totalized so it's not included
        this.setCalc(['total','MWh'],  this.getData(['usage','MWh']) );    }

    //
    // Add function for recalculation after change in price from Plane or Solid
      public pushRevRate(newRate: number): void {
        this.setCalc(['price','energy','atc'], newRate);
        this.recalculate()
      }
      
      public recalculate():void {
        // update revenues
        this.setCalc(['revenue','energy'],   this.getCalc(['price','energy','atc']) * this.getData(['usage','MWh','atc']) ); 
        this.setCalc(['revenue','demand'],   this.getCalc(['price','demand','PLC']) * this.getData(['demand','capacity','MW']) ); 
        this.setCalc(['revenue','flat'],     this.getCalc(['price','flat'])); 
        this.setCalc(['revenue','variable'], 0); 
        this.setCalc(['revenue','total'], this.totalizer(['revenue']));

        // update expenses that are dependent on revenue
        this.setCalc(['businessCost','revenueTax'], this.getData(['business','revTax']) * this.getCalc(['revenue','total']) );
        this.setCalc(['businessCost','total'], this.totalizer(['businessCost']) );

        // refresh totals
        this.prepTotal();
      }
  }