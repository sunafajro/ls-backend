(function(e){function t(t){for(var c,s,o=t[0],b=t[1],l=t[2],i=0,u=[];i<o.length;i++)s=o[i],Object.prototype.hasOwnProperty.call(a,s)&&a[s]&&u.push(a[s][0]),a[s]=0;for(c in b)Object.prototype.hasOwnProperty.call(b,c)&&(e[c]=b[c]);j&&j(t);while(u.length)u.shift()();return n.push.apply(n,l||[]),r()}function r(){for(var e,t=0;t<n.length;t++){for(var r=n[t],c=!0,o=1;o<r.length;o++){var b=r[o];0!==a[b]&&(c=!1)}c&&(n.splice(t--,1),e=s(s.s=r[0]))}return e}var c={},a={app:0},n=[];function s(t){if(c[t])return c[t].exports;var r=c[t]={i:t,l:!1,exports:{}};return e[t].call(r.exports,r,r.exports,s),r.l=!0,r.exports}s.m=e,s.c=c,s.d=function(e,t,r){s.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:r})},s.r=function(e){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},s.t=function(e,t){if(1&t&&(e=s(e)),8&t)return e;if(4&t&&"object"===typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(s.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var c in e)s.d(r,c,function(t){return e[t]}.bind(null,c));return r},s.n=function(e){var t=e&&e.__esModule?function(){return e["default"]}:function(){return e};return s.d(t,"a",t),t},s.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},s.p="/";var o=window["webpackJsonp"]=window["webpackJsonp"]||[],b=o.push.bind(o);o.push=t,o=o.slice();for(var l=0;l<o.length;l++)t(o[l]);var j=b;n.push([0,"chunk-vendors"]),r()})({0:function(e,t,r){e.exports=r("56d7")},4678:function(e,t,r){var c={"./af":"2bfb","./af.js":"2bfb","./ar":"8e73","./ar-dz":"a356","./ar-dz.js":"a356","./ar-kw":"423e","./ar-kw.js":"423e","./ar-ly":"1cfd","./ar-ly.js":"1cfd","./ar-ma":"0a84","./ar-ma.js":"0a84","./ar-sa":"8230","./ar-sa.js":"8230","./ar-tn":"6d83","./ar-tn.js":"6d83","./ar.js":"8e73","./az":"485c","./az.js":"485c","./be":"1fc1","./be.js":"1fc1","./bg":"84aa","./bg.js":"84aa","./bm":"a7fa","./bm.js":"a7fa","./bn":"9043","./bn-bd":"9686","./bn-bd.js":"9686","./bn.js":"9043","./bo":"d26a","./bo.js":"d26a","./br":"6887","./br.js":"6887","./bs":"2554","./bs.js":"2554","./ca":"d716","./ca.js":"d716","./cs":"3c0d","./cs.js":"3c0d","./cv":"03ec","./cv.js":"03ec","./cy":"9797","./cy.js":"9797","./da":"0f14","./da.js":"0f14","./de":"b469","./de-at":"b3eb","./de-at.js":"b3eb","./de-ch":"bb71","./de-ch.js":"bb71","./de.js":"b469","./dv":"598a","./dv.js":"598a","./el":"8d47","./el.js":"8d47","./en-au":"0e6b","./en-au.js":"0e6b","./en-ca":"3886","./en-ca.js":"3886","./en-gb":"39a6","./en-gb.js":"39a6","./en-ie":"e1d3","./en-ie.js":"e1d3","./en-il":"7333","./en-il.js":"7333","./en-in":"ec2e","./en-in.js":"ec2e","./en-nz":"6f50","./en-nz.js":"6f50","./en-sg":"b7e9","./en-sg.js":"b7e9","./eo":"65db","./eo.js":"65db","./es":"898b","./es-do":"0a3c","./es-do.js":"0a3c","./es-mx":"b5b7","./es-mx.js":"b5b7","./es-us":"55c9","./es-us.js":"55c9","./es.js":"898b","./et":"ec18","./et.js":"ec18","./eu":"0ff2","./eu.js":"0ff2","./fa":"8df4","./fa.js":"8df4","./fi":"81e9","./fi.js":"81e9","./fil":"d69a","./fil.js":"d69a","./fo":"0721","./fo.js":"0721","./fr":"9f26","./fr-ca":"d9f8","./fr-ca.js":"d9f8","./fr-ch":"0e49","./fr-ch.js":"0e49","./fr.js":"9f26","./fy":"7118","./fy.js":"7118","./ga":"5120","./ga.js":"5120","./gd":"f6b4","./gd.js":"f6b4","./gl":"8840","./gl.js":"8840","./gom-deva":"aaf2","./gom-deva.js":"aaf2","./gom-latn":"0caa","./gom-latn.js":"0caa","./gu":"e0c5","./gu.js":"e0c5","./he":"c7aa","./he.js":"c7aa","./hi":"dc4d","./hi.js":"dc4d","./hr":"4ba9","./hr.js":"4ba9","./hu":"5b14","./hu.js":"5b14","./hy-am":"d6b6","./hy-am.js":"d6b6","./id":"5038","./id.js":"5038","./is":"0558","./is.js":"0558","./it":"6e98","./it-ch":"6f12","./it-ch.js":"6f12","./it.js":"6e98","./ja":"079e","./ja.js":"079e","./jv":"b540","./jv.js":"b540","./ka":"201b","./ka.js":"201b","./kk":"6d79","./kk.js":"6d79","./km":"e81d","./km.js":"e81d","./kn":"3e92","./kn.js":"3e92","./ko":"22f8","./ko.js":"22f8","./ku":"2421","./ku.js":"2421","./ky":"9609","./ky.js":"9609","./lb":"440c","./lb.js":"440c","./lo":"b29d","./lo.js":"b29d","./lt":"26f9","./lt.js":"26f9","./lv":"b97c","./lv.js":"b97c","./me":"293c","./me.js":"293c","./mi":"688b","./mi.js":"688b","./mk":"6909","./mk.js":"6909","./ml":"02fb","./ml.js":"02fb","./mn":"958b","./mn.js":"958b","./mr":"39bd","./mr.js":"39bd","./ms":"ebe4","./ms-my":"6403","./ms-my.js":"6403","./ms.js":"ebe4","./mt":"1b45","./mt.js":"1b45","./my":"8689","./my.js":"8689","./nb":"6ce3","./nb.js":"6ce3","./ne":"3a39","./ne.js":"3a39","./nl":"facd","./nl-be":"db29","./nl-be.js":"db29","./nl.js":"facd","./nn":"b84c","./nn.js":"b84c","./oc-lnc":"167b","./oc-lnc.js":"167b","./pa-in":"f3ff","./pa-in.js":"f3ff","./pl":"8d57","./pl.js":"8d57","./pt":"f260","./pt-br":"d2d4","./pt-br.js":"d2d4","./pt.js":"f260","./ro":"972c","./ro.js":"972c","./ru":"957c","./ru.js":"957c","./sd":"6784","./sd.js":"6784","./se":"ffff","./se.js":"ffff","./si":"eda5","./si.js":"eda5","./sk":"7be6","./sk.js":"7be6","./sl":"8155","./sl.js":"8155","./sq":"c8f3","./sq.js":"c8f3","./sr":"cf1e","./sr-cyrl":"13e9","./sr-cyrl.js":"13e9","./sr.js":"cf1e","./ss":"52bd","./ss.js":"52bd","./sv":"5fbd","./sv.js":"5fbd","./sw":"74dc","./sw.js":"74dc","./ta":"3de5","./ta.js":"3de5","./te":"5cbb","./te.js":"5cbb","./tet":"576c","./tet.js":"576c","./tg":"3b1b","./tg.js":"3b1b","./th":"10e8","./th.js":"10e8","./tk":"5aff","./tk.js":"5aff","./tl-ph":"0f38","./tl-ph.js":"0f38","./tlh":"cf75","./tlh.js":"cf75","./tr":"0e81","./tr.js":"0e81","./tzl":"cf51","./tzl.js":"cf51","./tzm":"c109","./tzm-latn":"b53d","./tzm-latn.js":"b53d","./tzm.js":"c109","./ug-cn":"6117","./ug-cn.js":"6117","./uk":"ada2","./uk.js":"ada2","./ur":"5294","./ur.js":"5294","./uz":"2e8c","./uz-latn":"010e","./uz-latn.js":"010e","./uz.js":"2e8c","./vi":"2921","./vi.js":"2921","./x-pseudo":"fd7e","./x-pseudo.js":"fd7e","./yo":"7f33","./yo.js":"7f33","./zh-cn":"5c3a","./zh-cn.js":"5c3a","./zh-hk":"49ab","./zh-hk.js":"49ab","./zh-mo":"3a6c","./zh-mo.js":"3a6c","./zh-tw":"90ea","./zh-tw.js":"90ea"};function a(e){var t=n(e);return r(t)}function n(e){if(!r.o(c,e)){var t=new Error("Cannot find module '"+e+"'");throw t.code="MODULE_NOT_FOUND",t}return c[e]}a.keys=function(){return Object.keys(c)},a.resolve=n,e.exports=a,a.id="4678"},"56d7":function(e,t,r){"use strict";r.r(t);r("e260"),r("e6cf"),r("cca6"),r("a79d");var c=r("7a23"),a=(r("4de4"),{class:"row"}),n={class:"col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12"},s={key:0,class:"alert alert-warning"};function o(e,t,r,o,b,l){var j=Object(c["i"])("Sidebar"),i=Object(c["i"])("Content");return Object(c["g"])(),Object(c["c"])("div",a,[Object(c["f"])("div",n,[b.loading?(Object(c["g"])(),Object(c["c"])("div",s,"Идет загрузка данных...")):Object(c["d"])("",!0)]),b.loading?Object(c["d"])("",!0):(Object(c["g"])(),Object(c["c"])(j,{key:0,filter:b.filter,filters:b.filters,menu:b.menu,mode:b.mode,setFilter:l.setFilter,user:b.user},null,8,["filter","filters","menu","mode","setFilter","user"])),b.loading?Object(c["d"])("",!0):(Object(c["g"])(),Object(c["c"])(i,{key:1,columns:l.columns,rows:l.rows},null,8,["columns","rows"]))])}r("99af"),r("d3b7"),r("3ca3"),r("ddb0"),r("96cf");var b=r("1da1"),l=r("bc3a"),j=r.n(l),i=r("c1df"),u=r.n(i),d=r("7329"),f=r.n(d),O=(r("b0c0"),r("b64b"),r("53ca")),m={class:"col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10"},p={class:"small"},h={title:"Всего"},y=Object(c["f"])("i",{class:"fa fa-rub","aria-hidden":"true"},null,-1),g={class:"table table-striped table-bordered table-hover table-condensed small"},v={key:0},k={key:1},w={key:0},x={key:0,class:"text-center"},Y={title:"Всего"},z=Object(c["f"])("i",{class:"fa fa-rub","aria-hidden":"true"},null,-1);function D(e,t,r,a,n,s){var o=Object(c["i"])("breadcrumbs-component");return Object(c["g"])(),Object(c["c"])("div",m,[Object(c["f"])(o),(Object(c["g"])(!0),Object(c["c"])(c["a"],null,Object(c["h"])(Object.keys(r.rows),(function(e){return Object(c["g"])(),Object(c["c"])("div",{key:e},[Object(c["f"])("h3",null,[Object(c["e"])(Object(c["j"])(r.rows[e].name)+"   ",1),Object(c["f"])("div",p,[Object(c["f"])("span",h,[y,Object(c["e"])(" "+Object(c["j"])(s.formatNumber(r.rows[e].counts.all)),1)])])]),Object(c["f"])("table",g,[r.columns.length?(Object(c["g"])(),Object(c["c"])("thead",v,[(Object(c["g"])(!0),Object(c["c"])(c["a"],null,Object(c["h"])(s.visibleColumns,(function(e){return Object(c["g"])(),Object(c["c"])("th",{key:"th-".concat(e.id)},Object(c["j"])(e.name),1)})),128))])):Object(c["d"])("",!0),Array.isArray(r.rows[e].rows)&&r.rows[e].rows.length?(Object(c["g"])(),Object(c["c"])("tbody",k,[(Object(c["g"])(!0),Object(c["c"])(c["a"],null,Object(c["h"])(r.rows[e].rows,(function(e){return Object(c["g"])(),Object(c["c"])("tr",{key:"tr-".concat(e.id)},[(Object(c["g"])(!0),Object(c["c"])(c["a"],null,Object(c["h"])(s.visibleColumns,(function(t){return Object(c["g"])(),Object(c["c"])("td",{key:"td-".concat(e.id,"-").concat(t.id),width:t.width},[t.icon?Object(c["d"])("",!0):(Object(c["g"])(),Object(c["c"])("span",w,Object(c["j"])("date"!==t.id?e[t.id]:s.formatDate(e[t.id])),1)),t.icon&&e[t.id]?(Object(c["g"])(),Object(c["c"])("i",{key:1,class:t.icon+" "+e[t.id],"aria-hidden":"true"},null,2)):Object(c["d"])("",!0)],8,["width"])})),128))])})),128))])):Object(c["d"])("",!0)])])})),128)),"object"===Object(O["a"])(r.rows)&&Object.keys(r.rows).length>1?(Object(c["g"])(),Object(c["c"])("h3",x,[Object(c["f"])("div",null,[Object(c["f"])("span",Y,[z,Object(c["e"])(" "+Object(c["j"])(s.formatNumber(s.total.all)),1)])])])):Object(c["d"])("",!0)])}r("4160"),r("159b");var M=r("6612"),A=r.n(M),S={class:"breadcrumb"},I={key:1};function q(e,t,r,a,n,s){return Object(c["g"])(),Object(c["c"])("ul",S,[(Object(c["g"])(!0),Object(c["c"])(c["a"],null,Object(c["h"])(n.links,(function(e){return Object(c["g"])(),Object(c["c"])("li",{class:e.style,key:"ul-"+e.id},[e.url?(Object(c["g"])(),Object(c["c"])("a",{key:0,href:e.url},Object(c["j"])(e.text),9,["href"])):Object(c["d"])("",!0),e.url?Object(c["d"])("",!0):(Object(c["g"])(),Object(c["c"])("span",I,Object(c["j"])(e.text),1))],2)})),128))])}var P={data:function(){return{links:[{id:"home",style:null,text:"Главная",url:"/"},{id:"reports",style:null,text:"Отчеты",url:"/report/index"},{id:"payments",style:"active",text:"Отчет по оплатам",url:null}]}}};P.render=q;var R=P,F={components:{"breadcrumbs-component":R},computed:{total:function(){var e=this,t={all:0};return"object"===Object(O["a"])(this.rows)&&Object.keys(this.rows).length&&Object.keys(this.rows).forEach((function(r){t.all+=e.rows[r].counts.all})),t},visibleColumns:function(){return this.columns.filter((function(e){return e.show}))}},methods:{formatDate:function(e){return u()(e).format("DD.MM.YYYY")},formatNumber:function(e){return A()(e).format("0,0")}},props:{columns:{required:!0,type:Array},rows:{required:!0,type:Object}}};F.render=D;var _=F,N={class:"col-xs-12 col-sm-12 col-md-2 col-lg-2 col-xl-2"},C={key:0,class:"alert alert-danger"},U={key:2,class:"well well-sm small"},E={key:0},T={key:0},B={key:1},J={key:0},V={key:2},L={key:3},G={key:3,class:"dropdown"},H=Object(c["f"])("button",{type:"button",id:"dropdownMenu",class:"btn btn-default btn-sm btn-block dropdown-toggle","data-toggle":"dropdown","aria-haspopup":"true","aria-expanded":"true"},[Object(c["f"])("span",{class:"fa fa-list-alt","aria-hidden":"true"}),Object(c["e"])(" Отчеты "),Object(c["f"])("span",{class:"caret"})],-1),K={class:"dropdown-menu","aria-labelledby":"dropdownMenu"},Q={key:4},W=Object(c["f"])("h4",null,"Фильтры:",-1),X={class:"form-group"},Z={class:"form-group"},$=Object(c["f"])("div",{class:"form-group"},[Object(c["f"])("button",{type:"submit",class:"btn btn-sm btn-info btn-block"},[Object(c["f"])("span",{class:"fa fa-filter","aria-hidden":"true"}),Object(c["e"])(" Применить ")])],-1);function ee(e,t,r,a,n,s){var o=Object(c["i"])("nav-component");return Object(c["g"])(),Object(c["c"])("div",N,["object"!==Object(O["a"])(r.user)?(Object(c["g"])(),Object(c["c"])("div",C,"Ошибка загрузки данных!")):Object(c["d"])("",!0),"bitrix"===r.mode?(Object(c["g"])(),Object(c["c"])(o,{key:1})):Object(c["d"])("",!0),"object"===Object(O["a"])(r.user)&&Object.keys(r.user).length?(Object(c["g"])(),Object(c["c"])("div",U,[r.user.teacherId?Object(c["d"])("",!0):(Object(c["g"])(),Object(c["c"])("div",E,[r.user.name?(Object(c["g"])(),Object(c["c"])("b",T,Object(c["j"])(r.user.name),1)):Object(c["d"])("",!0)])),r.user.teacherId?(Object(c["g"])(),Object(c["c"])("div",B,[r.user.name?(Object(c["g"])(),Object(c["c"])("b",J,[Object(c["f"])("a",{href:"/teacher/view?id=".concat(r.user.teacherId)},Object(c["j"])(r.user.name),9,["href"])])):Object(c["d"])("",!0)])):Object(c["d"])("",!0),r.user.role?(Object(c["g"])(),Object(c["c"])("div",V,[Object(c["f"])("i",null,Object(c["j"])(r.user.role),1)])):Object(c["d"])("",!0),"4"===r.user.roleId?(Object(c["g"])(),Object(c["c"])("div",L,Object(c["j"])(r.user.office),1)):Object(c["d"])("",!0)])):Object(c["d"])("",!0),Array.isArray(r.menu)&&r.menu.length?(Object(c["g"])(),Object(c["c"])("div",G,[H,Object(c["f"])("ul",K,[(Object(c["g"])(!0),Object(c["c"])(c["a"],null,Object(c["h"])(r.menu,(function(e){return Object(c["g"])(),Object(c["c"])("li",{key:e.id},[Object(c["f"])("a",{class:"dropdown-item",href:e.url},Object(c["j"])(e.label),9,["href"])])})),128))])])):Object(c["d"])("",!0),"object"===Object(O["a"])(r.filters)&&Object.keys(r.filters).length?(Object(c["g"])(),Object(c["c"])("div",Q,[W,Object(c["f"])("form",{onSubmit:t[3]||(t[3]=Object(c["m"])((function(){return s.onSubmit.apply(s,arguments)}),["prevent"]))},[Object(c["f"])("div",X,[Array.isArray(r.filters.months)?Object(c["l"])((Object(c["g"])(),Object(c["c"])("select",{key:0,class:"form-control form-control-sm","onUpdate:modelValue":t[1]||(t[1]=function(e){return n.month=e})},[(Object(c["g"])(!0),Object(c["c"])(c["a"],null,Object(c["h"])(r.filters.months,(function(e){return Object(c["g"])(),Object(c["c"])("option",{key:"opt-".concat(e.value),value:e.value},Object(c["j"])(e.text),9,["value"])})),128))],512)),[[c["k"],n.month]]):Object(c["d"])("",!0)]),Object(c["f"])("div",Z,[Array.isArray(r.filters.years)?Object(c["l"])((Object(c["g"])(),Object(c["c"])("select",{key:0,class:"form-control form-control-sm","onUpdate:modelValue":t[2]||(t[2]=function(e){return n.year=e})},[(Object(c["g"])(!0),Object(c["c"])(c["a"],null,Object(c["h"])(r.filters.years,(function(e){return Object(c["g"])(),Object(c["c"])("option",{key:"opt-".concat(e.value),value:e.value},Object(c["j"])(e.text),9,["value"])})),128))],512)),[[c["k"],n.year]]):Object(c["d"])("",!0)]),$],32)])):Object(c["d"])("",!0)])}var te={class:"dropdown",style:{"margin-bottom":"0.5rem"}},re=Object(c["f"])("button",{class:"btn btn-default btn-block dropdown-toggle",type:"button",id:"mainDropdownMenu","data-toggle":"dropdown","aria-haspopup":"true","aria-expanded":"true"},[Object(c["f"])("i",{class:"fa fa-bars","aria-hidden":"true"}),Object(c["e"])(" Меню "),Object(c["f"])("span",{class:"caret"})],-1),ce={class:"dropdown-menu","aria-labelledby":"mainDropdownMenu"};function ae(e,t,r,a,n,s){return Object(c["g"])(),Object(c["c"])("div",te,[re,Object(c["f"])("ul",ce,[(Object(c["g"])(!0),Object(c["c"])(c["a"],null,Object(c["h"])(n.items,(function(e){return Object(c["g"])(),Object(c["c"])("li",{key:e.id},[Object(c["f"])("a",{href:e.url},[Object(c["f"])("i",{class:e.classes,"aria-hidden":"true"},null,2),Object(c["e"])(" "+Object(c["j"])(e.title),1)],8,["href"])])})),128))])])}var ne={created:function(){var e=this;return Object(b["a"])(regeneratorRuntime.mark((function t(){var r,c,a,n;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,j.a.get("/site/csrf");case 2:return r=t.sent,c=r.data,t.next=6,j.a.post("/site/nav",Object.assign({},c,{type:"all"}));case 6:a=t.sent,n=a.data,e.items=n.navElements;case 9:case"end":return t.stop()}}),t)})))()},data:function(){return{items:[]}}};ne.render=ae;var se=ne,oe={components:{"nav-component":se},created:function(){this.month=this.filter.month,this.year=this.filter.year},data:function(){return{month:"",week:"",year:""}},methods:{onSubmit:function(){this.setFilter({month:this.month,week:this.week,year:this.year})}},props:{filter:{required:!0,type:Object},filters:{required:!0,type:Object},menu:{required:!0,type:Array},mode:{required:!0,type:String},setFilter:{required:!0,type:Function},user:{required:!0,type:Object}}};oe.render=ee;var be=oe,le=function(){for(var e=[{text:"-выбрать месяц-",value:""}],t=u()(),r=0;r<12;r++)t.set("month",r),e.push({text:t.format("MMMM"),value:t.format("MM")});return e},je=function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:parseInt(u()().format("YYYY")),t=[];e=isNaN(e)?parseInt(u()().format("YYYY")):e;while(e<=parseInt(u()().format("YYYY")))t.push({text:String(e),value:String(e)}),e++;return t};u.a.locale("ru");var ie=document.getElementById("app"),ue=ie.dataset.urlPrefix,de={name:"app",components:{Content:_,Sidebar:be},computed:{columns:function(){return this.payments.columns},rows:function(){return this.payments.rows}},created:function(){var e=this;return Object(b["a"])(regeneratorRuntime.mark((function t(){var r;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.prev=0,t.next=3,Promise.all([e.getReportData(),e.getUserInfo()]);case 3:r=t.sent,e.menu=r[0].data.menuData,e.payments.columns=r[0].data.salariesData.columns,e.payments.rows=Array.isArray(r[0].data.salariesData.rows)?{}:r[0].data.salariesData.rows,e.user=r[1].data.userData,e.loading=!1,t.next=15;break;case 11:t.prev=11,t.t0=t["catch"](0),e.error=!0,new f.a({theme:"bootstrap-v3",text:"При загрузке данных произошла ошибка.",timeout:3e3,type:"danger",progressBar:!1}).show();case 15:case"end":return t.stop()}}),t,null,[[0,11]])})))()},data:function(){var e=u()().month();return{payments:{columns:[],rows:{}},error:!1,filter:{month:e>9?e:"0".concat(e),year:String(u()().year())},filters:{months:le(),years:je(this.nullYear)},loading:!0,menu:[],mode:ie.dataset.mode,nullYear:parseInt(ie.dataset.nullYear),user:{}}},methods:{getReportData:function(){var e=u()().startOf("week").format("YYYY-MM-DD"),t=u()().endOf("week").format("YYYY-MM-DD");return this.filter.month&&this.filter.year&&(e="".concat(this.filter.year,"-").concat(this.filter.month,"-01"),t=u()(e).endOf("month").format("YYYY-MM-DD")),j.a.post("".concat(ue,"/report/salaries?start=").concat(e,"&end=").concat(t))},getUserInfo:function(){return j.a.get("".concat(ue,"/user/app-info"))},setFilter:function(e){var t=this;return Object(b["a"])(regeneratorRuntime.mark((function r(){var c,a;return regeneratorRuntime.wrap((function(r){while(1)switch(r.prev=r.next){case 0:if(t.filter=e,!t.filter.month){r.next=7;break}return r.next=4,t.getReportData();case 4:c=r.sent,a=c.data,t.payments.rows=Array.isArray(a.salariesData.rows)?{}:a.salariesData.rows;case 7:case"end":return r.stop()}}),r)})))()}}};de.render=o;var fe=de;r("5fe7"),r("e625");Object(c["b"])(fe).mount("#app")}});
//# sourceMappingURL=app.js.map