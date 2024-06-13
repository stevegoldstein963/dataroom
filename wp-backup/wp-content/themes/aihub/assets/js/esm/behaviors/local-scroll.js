import a from"./base.js";class h extends a{static name="liquidLocalscroll";static model=new Backbone.Model(this.initialModelProps);options(){return{ease:"power2.inOut",duration:1,offset:null}}get ui(){return{links:"[data-lqd-local-scroll-el]"}}get domEvents(){return{"click @links":"onLinkClicked"}}initialize(){super.initialize(),this.watchers=[],this.initWatcher(),elementorFrontend?.utils?.anchors?.setSettings("selectors.targets",".to-kirekhar")}initWatcher(){const t=this.getUI("links");t.forEach((s,r)=>{const e=s.getAttribute("href");if(!e||e==="")return;const i=new URL(e,window.location.href).hash;if(!i)return;const o=document.querySelector(i);if(!o)return;const n=ScrollTrigger.create({trigger:o,start:"top center",end:"bottom center+=10px",onToggle:l=>{!l.isActive||(t.forEach(c=>c.classList.remove("lqd-is-active")),s.classList.add("lqd-is-active"))}});this.watchers.push(n)})}onLinkClicked(t){const r=t.currentTarget?.getAttribute("href");if(!r||r==="")return;const e=new URL(r,window.location.href).hash;if(!e)return;const i=document.querySelector(e);if(!i)return;t.preventDefault(),t.stopPropagation();const o=this.getOption("offset")||this.liquidApp.globalOptions.localScrollOffset,n=this.getOption("duration"),l=this.getOption("ease");this.scrollTween=gsap.to(window,{duration:n,ease:l,scrollTo:{y:i,offsetY:o,autoKill:!0}})}destroy(){this.scrollTween?.kill(),this.watchers?.forEach(t=>t.kill()),super.destroy()}}export default h;
