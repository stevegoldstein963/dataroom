import{DATA_ATTRS as a}from"../lib/consts.js";import g from"./base.js";class h extends g{static name="liquidAdaptiveColor";static model=new Backbone.Model(this.initialModelProps);get regionsEvents(){return{liquidPageContent:[{"change:computedStyles":"initialize"}]}}get windowEvents(){return{scroll:{func:"onScroll",throttle:{wait:150,options:{leading:!0}}}}}get bindToThis(){return["init","initWatch","detectCollision"]}initialize(){this.onPageContentBgChange=_.throttle(this.onPageContentBgChange,150),this.onScroll(),this.init()}init(){if(this.handleWidgetViewColorSchemeChange(),this.liquidApp.layoutRegions?.liquidPageContent?.model.get("behaviors").findIndex(t=>t.name==="liquidAdaptiveBackground")>=0)return this.listenToPageContentBgChange();this.initWatch()}handleWidgetViewColorSchemeChange(){this.view.model.on("change:colorScheme",(e,t)=>{t?this.view.el.setAttribute(a.COLOR_SCHEME.HTML_ATTR,t):this.view.el.removeAttribute(a.COLOR_SCHEME.HTML_ATTR)})}listenToPageContentBgChange(){const e=this.liquidApp.layoutRegions?.liquidPageContent?.model;!e||this.listenTo(e,"change:adaptiveBg",this.onPageContentBgChange)}onPageContentBgChange(e,{currentColor:t}){this.setWidgetModelColorScheme(this.getLuminosity(t.brightness))}initWatch(){if(!this.liquidApp.layoutRegions?.liquidPageContent)return;const e=this.liquidApp.layoutRegions.liquidPageContent.model.get("childrenCollection");if(!e)return;const t=e.filter(n=>n.get("isContainer"));!t.length||(this.pageContentContainers=t,this.scrollDirection=1,this.lastScrollPos=window.scrollY)}onScroll(){this.isDestroyed||!this.pageContentContainers?.length||(this.scrollDirection=this.lastScrollPos<=window.scrollY?1:-1,this.detectCollision(),this.lastScrollPos=window.scrollY)}detectCollision(){const{scrollY:e}=window,t=this.view.model.get("rect");if(!t)return;const n=this.pageContentContainers.map(i=>{const o=i.get("rect");return{cid:i.cid,rect:{...o,y:o.y-e,bottom:o.bottom-e}}}),s=n.filter(({rect:i})=>(this.scrollDirection>0?i.y>=t.y:i.y<=t.y)&&i.right>=t.right&&i.x<=t.x),l=n.filter(({rect:i})=>(this.scrollDirection>0?i.y<=t.y:i.y>=t.y)&&i.right>=t.right&&i.x<=t.x),r=this.scrollDirection>0?l[l.length-1]:s[s.length-1];!r||this.setWidgetModelColorScheme(this.getLuminosity(this.pageContentContainers.find(i=>i.cid===r.cid).get("brightness").backgroundColor))}getLuminosity(e){let t="light";return(!isNaN(e)&&e<=.5||e==="dark")&&(t="dark"),t}setWidgetModelColorScheme(e){fastdom.mutate(()=>this.view.model.set({colorScheme:e}))}destroy(){this.setWidgetModelColorScheme(null),super.destroy()}}export default h;