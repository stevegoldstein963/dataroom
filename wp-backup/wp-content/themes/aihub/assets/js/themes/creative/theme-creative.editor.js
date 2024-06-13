class LqdBaseHandler extends elementorModules.frontend.handlers.Base{static baseBehaviors=[];static optionsBehaviors=[];static paramsBehaviors=[];settingsModel=null;get baseBehaviors(){return this.constructor.baseBehaviors}get optionsBehaviors(){return this.constructor.optionsBehaviors}get paramsBehaviors(){const e=[...this.constructor.paramsBehaviors],t=this.getID(),i=this.getSettingsModel(),s=i.get("show_button")||i.get("ib_show_button"),n=i.get("trigger_enable")||i.get("ib_trigger_enable"),d=i.get("lqd_inview")==="yes",p=i.get("lqd_parallax")==="yes",g=i.get("link_type")||i.get("ib_link_type"),r=["lqd-menu","lqd-modal","lqd-trigger"],h=this.getWidgetType(),C=r.find(l=>l===h);if(C&&n&&n==="yes"&&e.push(...this.getTriggerBehaviors(C)),s&&s==="yes"&&g&&g==="local_scroll"){const l={},c=i.get("local_scroll_offset")||i.get("ib_local_scroll_offset");c?.size&&c?.size!==""&&(l.offset=c.size),e.push({behaviorClass:LiquidLocalScrollBehavior,options:l})}return[{domain:"parallax",isEnabled:p},{domain:"inview",isEnabled:d}].forEach(({domain:l,isEnabled:c})=>{if(!c){const a=`lqd-${l}-keyframe-${t}`;document.head.querySelector(`.${a}`)?.remove();return}e.push({behaviorClass:LiquidGetElementComputedStylesBehavior,options:{includeSelf:!0,getRect:!0,addGhosts:!0}}),e.push(this.handleAnimationsAndParallax(l,h))}),e}get liquidModelCID(){return this.$element.attr("data-lqd-model-cid")}onInit(...e){this.liquidApp=window.liquid.app,this.element=this.$element[0],super.onInit(...e),this.handleParallaxToggle(),this.handleAnimationsToggle(),elementor.channels.editor.on(`liquid:help-me:dark-color:${this.getID()}`,this.helpWithDarkColors.bind(this)),this.initBehaviors(),this.changed=!1}helpWithDarkColors(e,t){const i=t.currentTarget.dataset.action,s={};if(window.liquid.helpRequests[`element-${this.getID()}:liquid_help_dark_colors`].colorManipulations=window.liquid.helpRequests[`element-${this.getID()}:liquid_help_dark_colors`].colorManipulations||{},i){let[r,h]=i.split(":"),C=8,l=0;const c=window.liquid.helpRequests[`element-${this.getID()}:liquid_help_dark_colors`].colorManipulations[r];c!=null?h==="+"?l=c+C:l=c-C:l=C,s[r]=l,window.liquid.helpRequests[`element-${this.getID()}:liquid_help_dark_colors`].colorManipulations[r]=l}else s.saturate=-30,s.lighten=-6,window.liquid.helpRequests[`element-${this.getID()}:liquid_help_dark_colors`].colorManipulations.saturate=-30,window.liquid.helpRequests[`element-${this.getID()}:liquid_help_dark_colors`].colorManipulations.lighten=-6;const n=this.getSettingsModel(),d=Object.values(n.controls).filter(r=>!r.name.startsWith("_")&&(r.type==="color"||r.type==="liquid-color"||r.type==="liquid-background"||r.type==="liquid-background-css")),p=d.filter(r=>r.name.startsWith("dark_")),g={};p.forEach(r=>{const h=r.name.split("_")[1];g[h]||(g[h]=[]),g[h].push(r)}),p.forEach(r=>{const{name:h}=r,C=h.replace("dark_",""),l=d.find(q=>q.name===C),c=h.split("_");if(!l)return;const a=n.get(l.name);let u=a?.color||a;const f=c[1],o=h.match(/^((?!_background|_border).)*(?:_color(?:_hover|_active)?)$/);if(!o&&(!u||u===""))return;let y=[],m=null,b=null;const B=h.includes("_hover"),M=h.includes("_active");if(h.match(/(background)(_hover|_active)?(_color)/)&&(n.setExternalChange(h.replace("_color","_background"),"color"),f==="item"?Object.keys(g).filter(w=>w!=="item").forEach(w=>{y.push(g[w].find(T=>T.name.includes(`${w}_color${`${B?"_hover":M?"_active":""}`}`))||g[w].find(T=>T.name.includes(`${w}_color`)))}):y.push(g[f].find(q=>q.name.includes(`${f}_color${`${B?"_hover":M?"_active":""}`}`))||g[f].find(q=>q.name.includes(`${f}_color`)))),h.match(/(border)(_hover|_active)?(_color)/)){const q=h.replace("_color","_border");n.setExternalChange(q,n.get(q.replace("dark_","")))}if(o&&(m=g[f].find(q=>q.name.includes(`${f}_background${`${B?"_hover":M?"_active":""}`}`)),b=n.get(m.name),b===""&&!B&&!M&&(m=g.item.find(q=>q.name.includes(`item_background${`${B?"_hover":M?"_active":""}`}_color`)),b=n.get(m.name),b===""&&(u="#ffffff"))),!u||u=="")return;const x=setTimeout(()=>{let q=tinycolor(u);Object.entries(window.liquid.helpRequests[`element-${this.getID()}:liquid_help_dark_colors`].colorManipulations).forEach(([T,$])=>q=q[T]($));const w=q.toHexString();if(n.setExternalChange(h,w),y.length){const T=tinycolor(w).darken(85).toHexString(),$=tinycolor(w).lighten(85).toHexString();y.forEach(L=>{!L||n.setExternalChange(L.name,tinycolor.mostReadable(w,[T,$]).toHexString())})}x&&clearTimeout(x)},0)})}clearStyles(){this.getAllWidgetControls().forEach(e=>this.clearControlByType(e))}clearControlByType(e){const{type:t,name:i}=e,s=this.getSettingsModel();switch(t){case"dimensions":s.setExternalChange(i,{top:"",right:"",bottom:"",left:"",unit:e?.size_units[0]||"px",isLinked:!0});break;case"liquid-linked-dimensions":s.setExternalChange(i,{width:"",height:"",unit:e?.size_units[0]||"px",isLinked:!0});break;case"select":case"color":case"liquid-color":s.setExternalChange(i,e?.default||"");break;case"box_shadow":s.setExternalChange(`${i}_type`,"");break}}getAllWidgetControls(){return this.getSettingsModel().getStyleControls().filter(i=>i.type!=="repeater"&&!i.name.startsWith("_"))}getTriggerBehaviors(e){const t=[],i=this.getID();return e==="lqd-modal"&&t.push({behaviorClass:LiquidToggleBehavior,options:{ui:{togglableTriggers:".lqd-trigger",togglableElements:`.lqd-modal-${i}`},changePropPrefix:`lqdModalToggle-${i}`,toggleOffActiveItem:!0,changeTargetClassname:["opacity-0","invisible","pointer-events-none"]}}),t}handleAnimationEditButton(e=!1){const t=this.element.querySelector(".elementor-element-overlay .elementor-editor-element-settings");if(!t)return;const i=t.querySelector(".elementor-editor-element-inview-animations");if(i&&e)return;if(!e){i?.remove();return}const s=this.element.classList.contains("e-con"),n=this.element.parentElement?.closest(".e-con"),d=t.querySelector(".elementor-editor-element-edit"),p=d.cloneNode(!0);if(p.style.width="25px",p.style.fontSize="11px",t.insertAdjacentElement(s?"afterbegin":"beforeend",p),p.classList.add("elementor-editor-element-inview-animations"),p.setAttribute("title","Play inview animations"),p.innerHTML='<i aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M6 4v16a1 1 0 0 0 1.524 .852l13 -8a1 1 0 0 0 0 -1.704l-13 -8a1 1 0 0 0 -1.524 .852z" stroke-width="0" fill="currentColor"></path> </svg ></i>',n){const g=n.querySelector(".elementor-editor-element-settings");if(!g.querySelector(".elementor-editor-element-children-inview-animations")){const r=d.cloneNode(!0);r.style.width="25px",r.style.fontSize="11px",r.style.margin="0",r.style.zIndex="auto",g.insertAdjacentElement("afterbegin",r),r.classList.remove("elementor-editor-element-edit"),r.classList.add("elementor-editor-element-children-inview-animations"),r.setAttribute("title","Play children inview animations"),r.innerHTML='<i class="flex" aria-hidden="true"><svg style="margin: 0 -1px; xmlns="http://www.w3.org/2000/svg" width="8" height="8" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M6 4v16a1 1 0 0 0 1.524 .852l13 -8a1 1 0 0 0 0 -1.704l-13 -8a1 1 0 0 0 -1.524 .852z" stroke-width="0" fill="currentColor"></path> </svg ><svg style="margin: 0 -1px; xmlns="http://www.w3.org/2000/svg" width="8" height="8" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M6 4v16a1 1 0 0 0 1.524 .852l13 -8a1 1 0 0 0 0 -1.704l-13 -8a1 1 0 0 0 -1.524 .852z" stroke-width="0" fill="currentColor"></path> </svg ><svg style="margin: 0 -1px; xmlns="http://www.w3.org/2000/svg" width="8" height="8" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M6 4v16a1 1 0 0 0 1.524 .852l13 -8a1 1 0 0 0 0 -1.704l-13 -8a1 1 0 0 0 -1.524 .852z" stroke-width="0" fill="currentColor"></path> </svg ></i>',r.addEventListener("click",h=>{h.preventDefault();const l=h.currentTarget.closest("[data-lqd-model-cid]").getAttribute("data-lqd-model-cid"),a=liquid.app.elementsCollection.find(u=>u.cid===l).get("childrenCollection");if(a?.length){const u=a.map(f=>{const o=f.get("behaviors");if(!o)return;const y=o.filter(b=>b.name==="liquidAnimations"&&b.getOption("domain")==="inview");if(!y)return;const m=[];return y.forEach(b=>{const B=b.repeatTimelines||b.animationsBreakpoints;if(B.length===1)m.push(B[0].timeline);else{const M=B.find(x=>window.matchMedia(x.value).matches);m.push(M.timeline)}}),m}).flat();u.length&&u.forEach(f=>f?.restart(!0))}})}}p.addEventListener("click",g=>{const h=g.currentTarget.closest("[data-lqd-model-cid]").getAttribute("data-lqd-model-cid"),l=liquid.app.elementsCollection.find(a=>a.cid===h).get("behaviors")?.find(a=>a.name==="liquidAnimations"&&a.getOption("domain")==="inview"),c=setTimeout(()=>{elementor.panel.$el.find(".elementor-tab-control-advanced[data-tab=advanced]").triggerHandler("click"),clearTimeout(c);const a=setTimeout(()=>{if(elementor.panel.$el.find(".elementor-control-lqd_animations_section:not(.e-open)").triggerHandler("click"),clearTimeout(a),!l)return;let u,f;l.repeatTimelines?.length?u=l.repeatTimelines:l.animationsBreakpoints?.length&&(u=l.animationsBreakpoints),u&&_.defer(()=>{u.length===1?f=u[0].timeline:f=u.find(y=>window.matchMedia(y.value).matches).timeline,f?.restart(!0)})},5)},5)})}handleAnimationsAndParallax(e="inview",t){const i=this.getSettingsModel(),s=i.get(`lqd_${e}_preset`),n=this.getID(),d=`lqd-${e}-keyframe-${n}`,p=this.getWidgetType();let g={},r={},h="self";this.element.querySelectorAll("[data-lqd-split-type]").forEach(c=>c.closest(".elementor-widget").classList.add("lqd-el-has-inner-animatables"));let l={trigger:i.get(`lqd_${e}_trigger`),ease:i.get(`lqd_${e}_settings_ease`),domain:e,start:i.get(`lqd_${e}_settings_start`),startElementOffset:i.get(`lqd_${e}_settings_startElementOffset`).size,startViewportOffset:i.get(`lqd_${e}_settings_startViewportOffset`).size};if(i.get(`lqd_${e}_settings_start`)==="percentage"?l.start=i.get(`lqd_${e}_settings_start_percentage`).size:i.get(`lqd_${e}_settings_start`)==="custom"&&(l.start=i.get(`lqd_${e}_settings_start_custom`)),e==="inview"?(l.stagger={each:i.get(`lqd_${e}_settings_stagger`).size,from:i.get(`lqd_${e}_settings_direction`)},l.delay=i.get(`lqd_${e}_settings_start_delay`).size,l.duration=i.get(`lqd_${e}_settings_duration`).size):(l.scrub=i.get(`lqd_${e}_settings_scrub`).size,l.end=i.get(`lqd_${e}_settings_end`),l.endElementOffset=i.get(`lqd_${e}_settings_endElementOffset`).size,l.endViewportOffset=i.get(`lqd_${e}_settings_endViewportOffset`).size,i.get(`lqd_${e}_settings_end`)==="percentage"?l.end=i.get(`lqd_${e}_settings_end_percentage`).size:i.get(`lqd_${e}_settings_end`)==="custom"&&(l.end=i.get(`lqd_${e}_settings_end_custom`))),i.get(`lqd_${e}_settings_animation_repeat_enable`)&&(r.repeat=i.get(`lqd_${e}_settings_animation_repeat`).size,r.repeatDelay=i.get(`lqd_${e}_settings_animation_repeat_delay`).size,r.yoyo=i.get(`lqd_${e}_settings_animation_yoyo`)==="yes",r.yoyoEase=i.get(`lqd_${e}_settings_animation_yoyo_ease`)==="yes"),l={...l,...r},s&&s!=="custom"){let c=[];switch(s){case"Fade In":c=[{opacity:0},{opacity:1}];break;case"Fade In Down":c=[{opacity:0,y:-150},{opacity:1,y:0}];break;case"Fade In Up":c=[{opacity:0,y:150},{opacity:1,y:0}];break;case"Fade In Left":c=[{opacity:0,x:-150},{opacity:1,x:0}];break;case"Fade In Right":c=[{opacity:0,x:150},{opacity:1,x:0}];break;case"Flip In Y":c=[{opacity:0,x:150,rotateY:30},{opacity:1,x:0,rotateY:0}];break;case"Flip In X":c=[{opacity:0,y:150,rotateX:-30},{opacity:1,y:0,rotateX:0}];break;case"Scale Up":c=[{opacity:0,scale:.75},{opacity:1,scale:1}];break;case"Scale Down":c=[{opacity:0,scale:1.25},{opacity:1,scale:1}];break}c.all={keyframes:c}}else{const c=Object.keys({all:{value:"all"},...elementorFrontend.config.responsive.activeBreakpoints}).map(a=>a);for(let a of c){const u=i.get(`lqd_${e}_keyframes_${a}`);let f=1;document.head.querySelector(`.${d}[data-device=${a}]`)?.remove(),u.forEach(o=>{let y={scaleX:o.get("scaleX").size,scaleY:o.get("scaleY").size,skewX:o.get("skewX").size,skewY:o.get("skewY").size,x:`${o.get("x").size}${o.get("x").unit}`,y:`${o.get("y").size}${o.get("y").unit}`,z:`${o.get("z").size}${o.get("z").unit}`,rotateX:o.get("rotateX").size,rotateY:o.get("rotateY").size,rotateZ:o.get("rotateZ").size,opacity:o.get("opacity").size};if(g[a]=g[a]||{keyframes:[]},i.get(`lqd_${e}_devices_popover_${a}`)==="yes"){const m={ease:i.get(`lqd_${e}_settings_${a}_ease`),duration:i.get(`lqd_${e}_settings_${a}_duration`).size,stagger:{each:i.get(`lqd_${e}_settings_${a}_stagger`).size},delay:i.get(`lqd_${e}_settings_${a}_start_delay`).size};if(i.get(`lqd_${e}_settings_${a}_animation_repeat_enable`)){const b={repeat:i.get(`lqd_${e}_settings_${a}_animation_repeat`).size,repeatDelay:i.get(`lqd_${e}_settings_${a}_animation_repeat_delay`).size,yoyo:i.get(`lqd_${e}_settings_${a}_animation_yoyo`)==="yes",yoyoEase:i.get(`lqd_${e}_settings_${a}_animation_yoyo_ease`)==="yes"};m={...m,...b}}g[a].options=m}if(o.get("options")==="yes"){const m={...y};o.get("duration")&&(m.duration=o.get("duration").size),o.get("delay")&&(m.delay=o.get("delay").size),y={...m,ease:o.get("ease")}}if(i.get(`lqd_${e}_enable_css`)==="yes")if(f>1)g[a].keyframes.push(y);else{let m=`.elementor-element-${n}`,b="";if(a!=="all"){const x=elementorFrontend.config.responsive.breakpoints[a];b=`media="(${x.direction}-width:${x.value}px)"`}if(e==="inview"){const x=i.get("lqd_text_split_type");x&&x!==""&&(m=`.elementor-element-${n} .lqd-split-text-${x==="words"?"words":"chars"}`)}let B=`${m} {
								transform: translate3d(${o.get("x").size}${o.get("x").unit},${o.get("y").size}${o.get("y").unit},${o.get("z").size}${o.get("z").unit})
								scale(${o.get("scaleX").size}, ${o.get("scaleY").size})
								skew(${o.get("skewX").size}deg, ${o.get("skewY").size}deg)
								rotateX(${o.get("rotateX").size}deg)
								rotateY(${o.get("rotateY").size}deg)
								rotateZ(${o.get("rotateZ").size}deg);
								opacity: ${o.get("opacity").size};
							`;(o.get("transformOriginX").size!==50||o.get("transformOriginX").unit!=="%"||o.get("transformOriginY").size!==50||o.get("transformOriginY").unit!=="%"||o.get("transformOriginZ").size!==0)&&(B+=`transform-origin:
									${o.get("transformOriginX").size}${o.get("transformOriginX").unit}
									${o.get("transformOriginY").size}${o.get("transformOriginY").unit}
									${o.get("transformOriginZ").size}px;
								`);const M=`<style ${b} class="${d}" data-device="${a}">${B}</style>`;document.head.insertAdjacentHTML("beforeend",M)}else g[a].keyframes.push(y);f++})}}return l.animations=l.animations||[],e==="inview"&&i.get("lqd_text_split_type")!==""&&(h="selfAnimatables"),l.animations.push({elements:h,breakpointsKeyframes:g}),{behaviorClass:LiquidAnimationsBehavior,options:l}}handleParallaxToggle(){const t=this.getSettingsModel().get("lqd_parallax")==="yes"}handleAnimationsToggle(){const t=this.getSettingsModel().get("lqd_inview")==="yes";this.handleAnimationEditButton(t)}handleSplitTextToggle(){const t=this.getSettingsModel().get("lqd_text_split_type");if(this.lastSplitType!==t){const i=this.liquidApp.elementsCollection.get(this.liquidModelCID);if(!i)return;t===""&&i.set("animatableElements",[this.element])}this.lastSplitType=t}onElementChange(e){this.changed=e,e==="lqd_parallax"&&this.handleParallaxToggle(),e==="lqd_inview"&&this.handleAnimationsToggle(),e==="lqd_text_split_type"&&this.handleSplitTextToggle()}getModel(){const e=this.getModelCID();return elementorFrontend.config.elements.data[e]}getLiquidModel(){return this.liquidModel?this.liquidModel:(this.liquidModel=this.liquidApp.elementsCollection.find(e=>e.cid===this.liquidModelCID),this.liquidModel)}getSettingsModel(){if(this.settingsModel)return this.settingsModel;let e;const t=this.getModel();return this.isEdit&&t?e=t:e=new Backbone.Model(this.$element.data("settings")||{}),this.settingsModel=e,e}initBehaviors(){const e=[...this.baseBehaviors,...this.optionsBehaviors,...this.paramsBehaviors];this.liquidApp.isStarted?this.liquidApp.addElementBehaviors(this.element,e):window.liquid.behaviors=[...window.liquid.behaviors||[],{el:this.element,behaviors:e}]}destroyBehaviors(){this.liquidApp.destroyElementBehaviors({el:this.element}),this.constructor.optionsBehaviors=[],this.constructor.paramsBehaviors=[]}onDestroy(){const e=this.getID();this.destroyBehaviors(),elementor.channels.editor.off(`liquid:help-me:dark-color:${e}`),super.onDestroy(),["parallax","inview"].forEach(t=>{const i=`lqd-${t}-keyframe-${e}`;document.head.querySelector(`.${i}`)?.remove()}),this.changed=!1}}class LqdAccordionHandler extends LqdBaseHandler{static baseBehaviors=[{behaviorClass:LiquidToggleBehavior,options:{openedItems:[0],parentToChangeClassname:".lqd-accordion-item",toggleOffActiveItem:!0,changePropPrefix:"lqdAccordionToggle",ui:{togglableTriggers:".lqd-accordion-trigger",togglableElements:".lqd-accordion-content-wrap"}}},{behaviorClass:LiquidEffectsSlideToggleBehavior,options:{changePropPrefix:"lqdAccordionToggle"}}];get baseBehaviors(){const e=[...LqdAccordionHandler.baseBehaviors],t=e[0];return t.options.openedItems=[parseInt(this.getModel()?.get("active_item")||0,10)-1],e}onElementChange(e){switch(e){case"predefined_style":this.clearStyles(),this.handlePredefinedStyles();break}super.onElementChange(e)}handlePredefinedStyles(){const e=this.getElementSettings("predefined_style"),t=this.getSettingsModel();switch(t.get("expander_position")==="start"&&t.setExternalChange("expander_margin",{top:"0",right:"0.5",bottom:"0",left:"0",unit:"em",isLinked:!1}),e){case"style-1":this.predefinedStyle1(t);break;case"style-2":this.predefinedStyle2(t);break;case"style-3":this.predefinedStyle3(t);break;case"style-4":this.predefinedStyle4(t);break}}predefinedStyle1(e){e.setExternalChange("trigger_padding",{top:"0.65",right:"0",bottom:"0.65",left:"0",unit:"em",isLinked:!1}),e.setExternalChange("item_border_border","solid"),e.setExternalChange("item_border_width",{top:"0",right:"0",bottom:"1",left:"0",unit:"px",isLinked:!1}),e.setExternalChange("item_border_color","#00000017"),e.setExternalChange("content_padding",{top:"0",right:"0",bottom:"30",left:"0",unit:"px",isLinked:!1})}predefinedStyle2(e){e.setExternalChange("item_margin",{top:"0",right:"0",bottom:"20",left:"0",unit:"px",isLinked:!1}),e.setExternalChange("trigger_padding",{top:"20",right:"30",bottom:"20",left:"30",unit:"px",isLinked:!1}),e.setExternalChange("item_border_border","solid"),e.setExternalChange("item_border_width",{top:"1",right:"1",bottom:"1",left:"1",unit:"px",isLinked:!1}),e.setExternalChange("item_border_color","#00000017"),e.setExternalChange("content_padding",{top:"15",right:"30",bottom:"30",left:"30",unit:"px",isLinked:!1})}predefinedStyle3(e){e.setExternalChange("item_margin",{top:"0",right:"0",bottom:"20",left:"0",unit:"px",isLinked:!1}),e.setExternalChange("trigger_padding",{top:"20",right:"30",bottom:"20",left:"30",unit:"px",isLinked:!1}),e.setExternalChange("content_padding",{top:"15",right:"30",bottom:"30",left:"30",unit:"px",isLinked:!1}),e.setExternalChange("item_box_shadow_box_shadow_type","yes"),e.setExternalChange("item_box_shadow_box_shadow",{horizontal:"0",vertical:"0",blur:"15",spread:"0",color:"#0000000d"})}predefinedStyle4(e){e.setExternalChange("trigger_padding",{top:"20",right:"30",bottom:"20",left:"30",unit:"px",isLinked:!1}),e.setExternalChange("content_padding",{top:"30",right:"30",bottom:"10",left:"30",unit:"px",isLinked:!1}),e.setExternalChange("heading_background_active_background","color"),e.setExternalChange("heading_background_active_color","var(--e-global-color-primary)")}}class LqdBoxHandler extends LqdBaseHandler{static optionsBehaviors=[];get optionsBehaviors(){const e=[...LqdBoxHandler.optionsBehaviors],t=this.getSettingsModel().get("lqd_hover_3d_intensity");return t?.size>0&&e.push({behaviorClass:LiquidHover3dBehavior,options:{intensity:t.size,ui:{items:".elementor-widget-container"}}}),e}}class LqdButtonHandler extends LqdBaseHandler{}class LqdCarouselHandler extends LqdBaseHandler{static baseBehaviors=[{behaviorClass:LiquidCarouselBehavior}];static optionsBehaviors=[];get baseBehaviors(){const e=[...LqdCarouselHandler.baseBehaviors],t=this.getSettingsModel(),i={};return t.get("adaptive_height")==="yes"&&(i.adaptiveHeight=!0),t.get("equal_height")==="yes"&&(i.equalHeight=!0),t.get("cells_align")!=="start"&&(i.itemAlign=t.get("cells_align")),t.get("friction").size!==.28&&(i.friction=t.get("friction").size),t.get("selected_attraction").size!==.28&&(i.selectedAttraction=t.get("selected_attraction").size),t.get("wrap_around")==="yes"&&(i.wrapAround=!0),t.get("group_cells")==="yes"&&(i.groupItems=!0),t.get("connected_carousels")!==""&&(i.connectedCarousels=t.get("connected_carousels")),e[0].options=i,e}get optionsBehaviors(){const e=[...LqdCarouselHandler.optionsBehaviors],t=this.getSettingsModel();let i=!1;if(t.get("draggable")==="yes"){const s={};t.get("free_scroll")==="yes"&&(s.freeScroll=!0),t.get("free_scroll_friction").size!=.075&&(s.freeScrollFriction=t.get("free_scroll_friction").size),e.push({behaviorClass:LiquidCarouselDragBehavior,options:s})}if(t.get("nav_buttons")==="yes"&&e.push({behaviorClass:LiquidCarouselNavBehavior}),t.get("pagination_dots")==="yes"&&e.push({behaviorClass:LiquidCarouselDotsBehavior}),t.get("autoplay_time")>0){const s={autoplayTimeout:t.get("autoplay_time")};t.get("pause_autoplay_onhover")==="yes"&&(s.pauseAutoPlayOnHover=!0),e.push({behaviorClass:LiquidCarouselAutoplayBehavior,options:s})}return t.get("cells").every(s=>{if(!!s.attributes)return s?.get("cell_look_mouse")==="yes"?(i=!0,!1):!0}),i&&e.push({behaviorClass:LiquidLookAtMouseBehavior}),e}}class LqdContainerHandler extends LqdBaseHandler{onInit(){super.onInit(),this.handleSectionColorScheme()}onElementChange(e){switch(e){case"lqd_section_color_scheme":this.handleSectionColorScheme();break}super.onElementChange(e)}handleSectionColorScheme(){const t=this.getSettingsModel()?.get("lqd_section_color_scheme")||"";if(t==="")return this.element.removeAttribute(DATA_ATTRS.COLOR_SCHEME.HTML_ATTR);this.element.setAttribute(DATA_ATTRS.COLOR_SCHEME.HTML_ATTR,t)}}class LqdCounterHandler extends LqdBaseHandler{get optionsBehaviors(){const e=[...LqdCounterHandler.optionsBehaviors],t=this.getSettingsModel(),i=t.get("use_locale_string");if(t.get("dynamic_counter")==="yes")e.push({behaviorClass:LiquidDynamicRangeBehavior,options:{range:this.getRange(),el:".lqd-counter-el:not(.lqd-counter-placeholder)",placeholderEl:".lqd-counter-placeholder",hideElsIfNan:".lqd-counter-prefix, .lqd-counter-suffix",useLocaleString:i==="yes"}});else{const s={};if(t.get("count_from")!==""?s.countFrom=t.get("count_from"):s.countFrom=0,t.get("count_to")!==""&&(s.countTo=t.get("count_to")),t.get("use_locale_string")===""&&(s.useLocaleString=!1),t.get("count_duration")!==""&&(s.duration=t.get("count_duration")),t.get("count_delay")!==""&&(s.delay=t.get("count_delay")),t.get("dynamic_counter")==="yes"){const n=t.get("dynamic_counter_list")[0];n.counter_value&&n.counter_value!==""&&(s.countFrom=n.counter_value,s.countTo=n.counter_value)}e.push({behaviorClass:LiquidCounterBehavior,options:s})}return e}getRange(){const e={},i=this.getSettingsModel().get("dynamic_counter_list");if(i.length)for(const s of i){const n=s.get("counter_label"),d=s.get("counter_value");n===""||d===""||(e[n]=d)}return e}onInit(){if(super.onInit(),this.getSettingsModel().get("dynamic_counter")==="yes"){const e=this.liquidApp.elementsCollection.find(t=>t.cid===this.liquidModelCID);if(!e)return;e.get("topParentContainer")?.trigger("change:range",this.getRange())}}}class LqdDallEHandler extends LqdBaseHandler{}class LqdDarkSwitchHandler extends LqdBaseHandler{static baseBehaviors=[{behaviorClass:LiquidSwitchBehavior,options:{useLocalStorage:!0,attrs:[{key:"lqd-page-color-scheme",attr:DATA_ATTRS.PAGE_COLOR_SCHEME.HTML_ATTR,val:{on:"dark",off:"light"},el:"body"}]}}]}class LqdDrawShapeHandler extends LqdBaseHandler{static baseBehaviors=[{behaviorClass:LiquidDrawShapeBehavior,options:{}}];get baseBehaviors(){const e=[...LqdDrawShapeHandler.baseBehaviors],t=this.getSettingsModel(),i=e[0];return i.options={drawSVG:t.get("draw_from")!==""?t.get("draw_from"):"0% 0%",stagger:t.get("stagger")?.size?t.get("stagger").size:0,start:t.get("start")!==""?t.get("start"):"top bottom",end:t.get("end")!==""?t.get("end"):"center center",scrub:t.get("scrub")?t.get("scrub"):!0,ease:t.get("ease")},e}}class LqdDynamicRangeHandler extends LqdBaseHandler{static baseBehaviors=[{behaviorClass:LiquidRangeBehavior,options:{}}];get baseBehaviors(){const e=[...LqdDynamicRangeHandler.baseBehaviors],t=this.getSettingsModel(),i=e[0];return t.get("initial_value")!==""&&(i.set=[t.get("initial_value")]),t.get("dynamic_widgets_ids")!==""&&(i.getValuesFrom=t.get("dynamic_widgets_ids")),e}}class LqdGalleryHandler extends LqdBaseHandler{get optionsBehaviors(){const e=[...LqdGalleryHandler.optionsBehaviors],t=this.getSettingsModel(),i=t.get("lqd_hover_3d_intensity"),s=t.get("look_mouse");return t.get("layout")==="masonry"&&e.push({behaviorClass:LiquidMasonryBehavior}),s==="yes"&&e.push({behaviorClass:LiquidLookAtMouseBehavior}),i?.size>0&&e.push({behaviorClass:LiquidHover3dBehavior,options:{intensity:i.size}}),e}onInit(){super.onInit();const e=this.getSettingsModel();if(e.get("layout")!=="masonry")return;const t=liquid.breakpoints,i=["change:items_gap_masonry","change:items_width"];Object.entries(t).forEach(([n,{value:d}])=>{i.push(`change:items_gap_masonry_${n}`),i.push(`change:items_width_${n}`)});const s=_.debounce(async()=>{const d=this.getLiquidModel().get("behaviors")?.find(p=>p.name==="liquidMasonry");!d||await d.layout()},185.69,!1);i.forEach(n=>{e.on(n,s)})}}class LqdGlobalElementHandler extends elementorModules.frontend.handlers.Base{isAddedToLiquidApp=!1;changed=!1;get liquidModelCID(){return this.element.getAttribute("data-lqd-model-cid")}onInit(...e){this.liquidApp=window.liquid.app,this.element=this.$element[0],super.onInit(...e),this.liquidApp.isStarted&&(this.addToLiquidElementsCollection(),this.isAddedToLiquidApp=!0),this.changed=!1}onElementChange(e){this.changed=e}addToLiquidElementsCollection(){this.liquidModelCID||this.liquidApp.addToElementsCollection(this.element)}removeFromLiquidElementsCollection(){this.liquidApp.removeFromElementsCollection(this.element)}onDestroy(){!this.changed&&this.liquidModelCID&&this.removeFromLiquidElementsCollection(),super.onDestroy(),this.isAddedToLiquidApp=!1,this.changed=!1}}class LqdGlowHandler extends LqdBaseHandler{}class LqdImageHandler extends LqdBaseHandler{get optionsBehaviors(){const e=[...LqdImageHandler.optionsBehaviors],t=this.getSettingsModel(),i=t.get("lqd_hover_3d_intensity"),s=t.get("look_mouse");return i?.size>0&&e.push({behaviorClass:LiquidHover3dBehavior,options:{intensity:i.size}}),(i?.size===""||i?.size===0)&&s==="yes"&&e.push({behaviorClass:LiquidLookAtMouseBehavior}),e}}class LqdIntegrationHandler extends LqdBaseHandler{}class LqdLottieHandler extends LqdBaseHandler{static baseBehaviors=[{behaviorClass:LiquidLottieBehavior}];get baseBehaviors(){const e=[...LqdLottieHandler.baseBehaviors],t=e[0],i=this.getSettingsModel(),s=`lqd-lottie-${this.getID()}`,n=i.get("json_source");let d="";return n==="internal"?d=i.get("json_file")?.url?i.get("json_file").url:"":d=i.get("json_url")?.url?i.get("json_url").url:"",d===""?[]:(t.options={animType:i.get("render_type"),name:s,autoplay:i.get("autoplay")!=="",loop:i.get("loop")!=="",path:d,className:"lqd-lottie",direction:i.get("direction"),speed:i.get("animation_speed").size},e)}}class LqdMarqueeHandler extends LqdBaseHandler{static baseBehaviors=[{behaviorClass:LiquidMarqueeBehavior}];static optionsBehaviors=[];get baseBehaviors(){const e=[...LqdMarqueeHandler.baseBehaviors],t=this.getSettingsModel(),i={};return t.get("reverse")==="yes"&&(i.reversed=!0),t.get("interact_with_scroll")!=="yes"&&(i.interactWithScroll=!1),t.get("speed")!==1&&(i.speed=t.get("speed")),e[0].options=i,e}get optionsBehaviors(){const e=[...LqdMarqueeHandler.optionsBehaviors],t=this.getSettingsModel();let i=!1;return t.get("cells")?.every(s=>{if(!!s.attributes)return s?.get("cell_look_mouse")==="yes"?(i=!0,!1):!0}),i&&e.push({behaviorClass:LiquidLookAtMouseBehavior}),e}}class LqdMenuHandler extends LqdBaseHandler{static baseBehaviors=[{behaviorClass:LiquidToggleBehavior,options:{changePropPrefix:"lqdMenuSubmenu",disableOnTouch:!0,ui:{togglableTriggers:".menu-item-has-children",togglableElements:".lqd-dropdown"},triggerElements:["pointerenter @togglableTriggers","pointerleave @togglableTriggers"]}},{behaviorClass:LiquidDropdownBehavior,options:{changePropPrefix:"lqdMenuSubmenu"}},{behaviorClass:LiquidToggleBehavior,options:{changePropPrefix:"lqdMenuMobileToggle",ui:{togglableTriggers:".lqd-dropdown-trigger",togglableElements:".lqd-menu-dropdown"},triggerElements:["click @togglableTriggers"]}},{behaviorClass:LiquidDropdownBehavior,options:{changePropPrefix:"lqdMenuMobileToggle",keepHiddenClassname:!1}},{behaviorClass:LiquidGetElementComputedStylesBehavior,options:{getRect:!0,includeSelf:!0}},{behaviorClass:LiquidToggleBehavior,options:{changePropPrefix:"lqdMenuToggle",ui:{togglableTriggers:".lqd-trigger",togglableElements:".lqd-menu-wrap"},triggerElements:["click @togglableTriggers"]}},{behaviorClass:LiquidEffectsSlideToggleBehavior,options:{changePropPrefix:"lqdMenuToggle",keepHiddenClassname:!1}}];get optionsBehaviors(){const e=[...LqdMenuHandler.optionsBehaviors],t=this.getSettingsModel(),i=t.get("magnetic_items"),s=t.get("localscroll"),n=t.get("localscroll_offset");return i==="yes"&&e.push({behaviorClass:LiquidMagneticMouseBehavior}),s==="yes"&&e.push({behaviorClass:LiquidLocalScrollBehavior,options:{offset:n.size,ui:{links:".lqd-menu-link-top"}}}),e}}class LqdModalHandler extends LqdBaseHandler{static baseBehaviors=[{behaviorClass:LiquidToggleBehavior,options:{ui:{togglableElements:".lqd-modal"},changePropPrefix:"lqdModalToggle",toggleAllTriggers:!0,changeTargetClassname:["opacity-0","invisible","pointer-events-none"]}}];get optionsBehaviors(){const e=this.getSettingsModel(),t=[...LqdModalHandler.optionsBehaviors];if(e.get("modal_type")!=="box"){const i={behaviorClass:LiquidMoveElementBehavior,options:{elementsToMove:".lqd-modal",moveElementsTo:e.get("modal_type")==="in-container"?"parent":"body"}};t.push(i)}return t}onInit(...e){const t=`:scope > .lqd-modal-${this.getID()}`;document.body.querySelector(t)?.remove(),this.$element[0].closest(".e-con").querySelector(t)?.remove(),super.onInit(...e)}}class LqdNewslettersHandler extends LqdBaseHandler{}class LqdPostsListHandler extends LqdBaseHandler{}class LqdPriceTableHandler extends LqdBaseHandler{get optionsBehaviors(){const e=[...LqdCounterHandler.optionsBehaviors],t=this.getSettingsModel();return t.get("dynamic_price")==="yes"&&e.push({behaviorClass:LiquidDynamicRangeBehavior,options:{range:this.getRange(),el:".lqd-price-table-price-value",separator:t.get("currency_format"),appendSeparatedTo:".lqd-price-table-price-sup",hideElsIfNan:".lqd-price-table-currency, .lqd-price-table-price-sup, .lqd-price-table-period"}}),e}getRange(){const e={},i=this.getSettingsModel().get("dynamic_price_list");if(i.length)for(const s of i){const n=s.get("price_label"),d=s.get("price_value");n===""||d===""||(e[n]=d)}return e}onInit(){if(super.onInit(),this.getSettingsModel().get("dynamic_price")==="yes"){const e=this.liquidApp.elementsCollection.find(t=>t.cid===this.liquidModelCID);if(!e)return;e.get("topParentContainer")?.trigger("change:range",this.getRange())}}}class LqdSearchHandler extends LqdBaseHandler{static baseBehaviors=[{behaviorClass:LiquidToggleBehavior,options:{changePropPrefix:"lqdSearchToggle",toggleAllTriggers:!0,ignoreEnterOnFocus:!0,toggleOffOnEscPress:!0,toggleOffOnOutsideClick:!0,triggerElements:["click @togglableTriggers"]}},{behaviorClass:LiquidEffectsSlideToggleBehavior,options:{changePropPrefix:"lqdSearchToggle"}}]}class LqdSiteLogoHandler extends LqdBaseHandler{}class LqdStepsHandler extends LqdBaseHandler{}class LqdTabsHandler extends LqdBaseHandler{static baseBehaviors=[{behaviorClass:LiquidToggleBehavior,options:{openedItem:0,toggleOffActiveItem:!0,keepOneItemActive:!0,changePropPrefix:"lqdTabsToggle",ui:{togglableTriggers:".lqd-tabs-trigger",togglableElements:".lqd-tabs-content"}}},{behaviorClass:LiquidEffectsDisplayToggleBehavior,options:{changePropPrefix:"lqdTabsToggle"}},{behaviorClass:LiquidEffectsFadeToggleBehavior,options:{changePropPrefix:"lqdTabsToggle"}}];get baseBehaviors(){const e=[...LqdTabsHandler.baseBehaviors],t=e[0];return this.getSettingsModel().get("tab_trigger")==="hover"&&(t.options.triggerElements=["pointerenter @togglableTriggers"]),e}}class LqdTestimonialHandler extends LqdBaseHandler{}class LqdTextRotatorHandler extends LqdBaseHandler{static baseBehaviors=[{behaviorClass:LiquidGetElementComputedStylesBehavior,options:{getRect:!0,includeSelf:!0,elementsSelector:".lqd-text-rotator-item"}},{behaviorClass:LiquidTextRotatorBehavior,options:{}}];get baseBehaviors(){const e=LqdTextRotatorHandler.baseBehaviors,t=this.getSettingsModel(),i=e[1];return i.options={stayDuration:t.get("stay_duration")?.size||3,leaveDuration:t.get("leave_duration")?.size||.65,enterDuration:t.get("enter_duration")?.size||.65},e}get optionsBehaviors(){const e=[],i=this.getSettingsModel().get("lqd_text_split_type");return i&&i!==""&&e.push({behaviorClass:LiquidSplitTextBehavior,options:{splitDoneFromBackend:!0,splitType:i}}),e}onInit(...e){this.getSettingsModel().get("lqd_text_split_type")!==""?this.$element[0].classList.add("lqd-el-has-inner-animatables"):this.$element[0].classList.remove("lqd-el-has-inner-animatables"),super.onInit(...e)}}class LqdTextHandler extends LqdBaseHandler{get optionsBehaviors(){const e=[...LqdTextHandler.baseBehaviors],t=this.getSettingsModel(),i=t.get("lqd_text_content"),s=t.get("lqd_text_split_type");return s&&s!==""&&e.push({behaviorClass:LiquidSplitTextBehavior,options:{splitDoneFromBackend:!0,splitType:s}}),i?.forEach(n=>{n.get("image")?.url!==""&&n.get("look_mouse")==="yes"&&e.push({behaviorClass:LiquidLookAtMouseBehavior})}),e}onInit(...e){this.getSettingsModel().get("lqd_text_split_type")!==""?this.$element[0].classList.add("lqd-el-has-inner-animatables"):this.$element[0].classList.remove("lqd-el-has-inner-animatables"),super.onInit(...e)}}class LqdThrowableHandler extends LqdBaseHandler{static baseBehaviors=[{behaviorClass:LiquidThrowableBehavior}]}(()=>{"use strict";function v(e){const i=e.container.view.el.getAttribute("data-lqd-model-cid"),n=liquid.app.elementsCollection.find(g=>g.cid===i)?.get("behaviors")?.find(g=>g.name==="liquidAnimations"&&g.getOption("domain")==="inview");if(!n)return;let d,p;n.repeatTimelines?.length?d=n.repeatTimelines:n.animationsBreakpoints?.length&&(d=n.animationsBreakpoints),d&&_.defer(()=>{d.length===1?p=d[0].timeline:p=d.find(r=>window.matchMedia(r.value).matches).timeline,p?.restart(!0)})}window.addEventListener("elementor/frontend/init",()=>{elementorFrontend.hooks.addAction("frontend/element_ready/global",t=>{elementorFrontend.elementsHandler.addHandler(LqdGlobalElementHandler,{$element:t})}),elementorFrontend.hooks.addAction("frontend/element_ready/container",t=>{elementorFrontend.elementsHandler.addHandler(LqdContainerHandler,{$element:t})}),elementorFrontend.elementsHandler.attachHandler("lqd-accordion",LqdAccordionHandler),elementorFrontend.elementsHandler.attachHandler("lqd-box",LqdBoxHandler),elementorFrontend.elementsHandler.attachHandler("lqd-button",LqdButtonHandler),elementorFrontend.elementsHandler.attachHandler("lqd-carousel",LqdCarouselHandler),elementorFrontend.elementsHandler.attachHandler("lqd-counter",LqdCounterHandler),elementorFrontend.elementsHandler.attachHandler("lqd-dall-e",LqdDallEHandler),elementorFrontend.elementsHandler.attachHandler("lqd-dark-switch",LqdDarkSwitchHandler),elementorFrontend.elementsHandler.attachHandler("lqd-draw-shape",LqdDrawShapeHandler),elementorFrontend.elementsHandler.attachHandler("lqd-dynamic-range",LqdDynamicRangeHandler),elementorFrontend.elementsHandler.attachHandler("lqd-glow",LqdGlowHandler),elementorFrontend.elementsHandler.attachHandler("lqd-gallery",LqdGalleryHandler),elementorFrontend.elementsHandler.attachHandler("lqd-image",LqdImageHandler),elementorFrontend.elementsHandler.attachHandler("lqd-integration",LqdIntegrationHandler),elementorFrontend.elementsHandler.attachHandler("lqd-lottie",LqdLottieHandler),elementorFrontend.elementsHandler.attachHandler("lqd-marquee",LqdMarqueeHandler),elementorFrontend.elementsHandler.attachHandler("lqd-menu",LqdMenuHandler),elementorFrontend.elementsHandler.attachHandler("lqd-modal",LqdModalHandler),elementorFrontend.elementsHandler.attachHandler("lqd-newsletters",LqdNewslettersHandler),elementorFrontend.elementsHandler.attachHandler("lqd-posts-list",LqdPostsListHandler),elementorFrontend.elementsHandler.attachHandler("lqd-price-table",LqdPriceTableHandler),elementorFrontend.elementsHandler.attachHandler("lqd-search",LqdSearchHandler),elementorFrontend.elementsHandler.attachHandler("lqd-steps",LqdStepsHandler),elementorFrontend.elementsHandler.attachHandler("lqd_site_logo",LqdSiteLogoHandler),elementorFrontend.elementsHandler.attachHandler("lqd-tabs",LqdTabsHandler),elementorFrontend.elementsHandler.attachHandler("lqd-testimonial",LqdTestimonialHandler),elementorFrontend.elementsHandler.attachHandler("lqd-text",LqdTextHandler),elementorFrontend.elementsHandler.attachHandler("lqd-text-rotator",LqdTextRotatorHandler),elementorFrontend.elementsHandler.attachHandler("lqd-throwable",LqdThrowableHandler);const e=[...elementor.$previewContents[0].body.classList].find(t=>t.startsWith("single-liquid"));if(e){const t=elementor.$previewWrapper[0].closest("body"),i=[...t.classList].find(s=>s.startsWith("single-liquid"));t.classList.remove(i),t.classList.add(e)}elementorFrontend.on("components:init",()=>{liquid.breakpoints=elementor.breakpoints.responsiveConfig.activeBreakpoints,_.defer(()=>{liquid.app.start({isEditor:!0}),liquid.app.on("app:start",()=>{_.defer(()=>{liquid.app.addBehaviors(),liquid.app.initializeBehaviors()}),_.defer(()=>{liquid.app.isStarted=!0})})}),elementor.channels.editor.on("liquid:inview:play",v)})})})();