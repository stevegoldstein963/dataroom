class LiquidMarqueeBehavior extends LiquidBehavior{static name="liquidMarquee";static model=new Backbone.Model(this.initialModelProps);options(){return{reversed:!1,speed:1,interactWithScroll:!0}}get ui(){return{items:".lqd-marquee-cell"}}get bindToThis(){return["sizing"]}initialize(){this.isPaused=!0,this.size={width:0},this.items=this.getUI("items").map(e=>({el:e,size:{width:0},positition:{x:0}})),this.init()}async init(){this.isDestroyed||(await fastdom.measure(this.sizing),!this.isDestroyed&&(this.buildTimeline(),this.setIntersectionObserver(),this.getOption("interactWithScroll")&&this.interactWithScroll()))}sizing(){this.isDestroyed||(this.size.width=this.view.el.offsetWidth,this.items.forEach(e=>{e.size={width:e.el.offsetWidth},e.positition={x:e.el.offsetLeft}}),this.startX=this.items[0].positition.x-parseInt(getComputedStyle(this.items[0].el).marginInlineEnd,10))}buildTimeline(){const e=this.getItemElements();this.timeline=gsap.timeline({repeat:-1,paused:!0,defaults:{ease:"none"},onReverseComplete:()=>this.timeline.totalTime(this.timeline.rawTime()+this.timeline.duration()*100)});const i=this.items.length,l=this.items[i-1],o=[],a=[],h=this.getOption("speed")*100,m=t=>t;let c,r,d,n;gsap.set(e,{xPercent:(t,s)=>{const p=this.items[t].size.width;return a[t]=m(parseFloat(gsap.getProperty(s,"x","px"))/p*100+gsap.getProperty(s,"xPercent")),a[t]},x:0}),c=l.positition.x+a[i-1]/100*l.size.width-this.startX+l.size.width*gsap.getProperty(e[i-1],"scaleX");for(let t=0;t<i;t++){const s=this.items[t];r=a[t]/100*s.size.width,d=s.positition.x+r-this.startX,n=d+s.size.width*gsap.getProperty(s.el,"scaleX"),this.timeline.to(s.el,{xPercent:m((r-n)/s.size.width*100),duration:n/h},0).fromTo(s.el,{xPercent:m((r-n+c)/s.size.width*100)},{xPercent:a[t],duration:(r-n+c-r)/h,immediateRender:!1},n/h).add("label"+t,d/h),o[t]=d/h}this.timeline.times=o,this.timeline.progress(1,!0).progress(0,!0),this.getOption("reversed")&&this.timeline.vars.onReverseComplete()}play(){this.setWillChange("transform"),this.getOption("reversed")?this.timeline.reverse():this.timeline.play(),this.isPaused=!1}pause(){this.setWillChange(""),this.timeline.pause(),this.isPaused=!0}setIntersectionObserver(){new IntersectionObserver(([e],i)=>{if(this.isDestroyed)return i.disconnect();e.isIntersecting?this.play():this.pause()}).observe(this.view.el)}interactWithScroll(){const i=this.getOption("reversed")?-1:1,l=gsap.utils.clamp(-6,6);this.STTween=gsap.to(this.timeline,{duration:1.5,timeScale:i,paused:!0}),this.ST=ScrollTrigger.create({start:0,end:"max",onUpdate:o=>{this.isPaused||(this.timeline.timeScale(l(o.getVelocity()/200*i)),this.STTween.invalidate().restart())}})}setWillChange(e){this.getItemElements().forEach(i=>i.style.willChange=e)}getItemElements(){return this.items.map(e=>e.el)}destroy(){this.timeline.kill(),this.STTween&&this.STTween.kill(),this.ST&&this.ST.kill(),this.setWillChange(""),super.destroy()}}