class LiquidDrawShapeBehavior extends LiquidBehavior{static name="liquidDrawShape";static model=new Backbone.Model(this.initialModelProps);options(){return{drawSVG:"0% 0%",duration:5,stagger:1,delay:0,start:"top bottom",end:"center center",scrub:!0,ease:"linear"}}get ui(){return{svgEl:"svg"}}initialize(){if(this.isDestroyed)return;const e=this.getOption("drawSVG"),i=this.getOption("duration"),s=this.getOption("stagger"),r=this.getOption("delay"),n=this.getOption("ease"),o=this.getOption("start"),a=this.getOption("end"),g=this.getOption("scrub"),t=this.getUI("svgEl");!t.length||(this.timeline=gsap.from(t[0].querySelectorAll(":scope > *"),{drawSVG:e,duration:i,delay:r,stagger:s,ease:n,scrollTrigger:{trigger:this.view.el,start:o,end:a,scrub:g}}))}destroy(){this.timeline?.revert(),super.destroy()}}