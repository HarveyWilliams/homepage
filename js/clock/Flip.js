/*! FlipClock 2014-01-20 */
var Base=function(){};Base.extend=function(a,b){"use strict";var c=Base.prototype.extend;Base._prototyping=!0;var d=new this;c.call(d,a),d.base=function(){},delete Base._prototyping;var e=d.constructor,f=d.constructor=function(){if(!Base._prototyping)if(this._constructing||this.constructor==f)this._constructing=!0,e.apply(this,arguments),delete this._constructing;else if(null!==arguments[0])return(arguments[0].extend||c).call(arguments[0],d)};return f.ancestor=this,f.extend=this.extend,f.forEach=this.forEach,f.implement=this.implement,f.prototype=d,f.toString=this.toString,f.valueOf=function(a){return"object"==a?f:e.valueOf()},c.call(f,b),"function"==typeof f.init&&f.init(),f},Base.prototype={extend:function(a,b){if(arguments.length>1){var c=this[a];if(c&&"function"==typeof b&&(!c.valueOf||c.valueOf()!=b.valueOf())&&/\bbase\b/.test(b)){var d=b.valueOf();b=function(){var a=this.base||Base.prototype.base;this.base=c;var b=d.apply(this,arguments);return this.base=a,b},b.valueOf=function(a){return"object"==a?b:d},b.toString=Base.toString}this[a]=b}else if(a){var e=Base.prototype.extend;Base._prototyping||"function"==typeof this||(e=this.extend||e);for(var f={toSource:null},g=["constructor","toString","valueOf"],h=Base._prototyping?0:1;i=g[h++];)a[i]!=f[i]&&e.call(this,i,a[i]);for(var i in a)f[i]||e.call(this,i,a[i])}return this}},Base=Base.extend({constructor:function(){this.extend(arguments[0])}},{ancestor:Object,version:"1.1",forEach:function(a,b,c){for(var d in a)void 0===this.prototype[d]&&b.call(c,a[d],d,a)},implement:function(){for(var a=0;a<arguments.length;a++)"function"==typeof arguments[a]?arguments[a](this.prototype):this.prototype.extend(arguments[a]);return this},toString:function(){return String(this.valueOf())}});var FlipClock;!function(a){"use strict";FlipClock=function(a,b,c){return new FlipClock.Factory(a,b,c)},FlipClock.Lang={},FlipClock.Base=Base.extend({buildDate:"2013-11-07",version:"0.3.1",constructor:function(b,c){"object"!=typeof b&&(b={}),"object"!=typeof c&&(c={}),this.setOptions(a.extend(!0,{},b,c))},callback:function(a){if("function"==typeof a){for(var b=[],c=1;c<=arguments.length;c++)arguments[c]&&b.push(arguments[c]);a.apply(this,b)}},log:function(a){window.console&&console.log&&console.log(a)},getOption:function(a){return this[a]?this[a]:!1},getOptions:function(){return this},setOption:function(a,b){this[a]=b},setOptions:function(a){for(var b in a)"undefined"!=typeof a[b]&&this.setOption(b,a[b])}}),FlipClock.Factory=FlipClock.Base.extend({autoStart:!0,callbacks:{destroy:!1,create:!1,init:!1,interval:!1,start:!1,stop:!1,reset:!1},classes:{active:"flip-clock-active",before:"flip-clock-before",divider:"flip-clock-divider",dot:"flip-clock-dot",label:"flip-clock-label",flip:"flip",play:"play",wrapper:"flip-clock-wrapper"},clockFace:"HourlyCounter",defaultClockFace:"HourlyCounter",defaultLanguage:"english",language:"english",lang:!1,face:!0,running:!1,time:!1,timer:!1,lists:[],$wrapper:!1,constructor:function(b,c,d){this.lists=[],this.running=!1,this.base(d),this.$wrapper=a(b).addClass(this.classes.wrapper),this.time=new FlipClock.Time(this,c?Math.round(c):0),this.timer=new FlipClock.Timer(this,d),this.lang=this.loadLanguage(this.language),this.face=this.loadClockFace(this.clockFace,d),this.autoStart&&this.start()},loadClockFace:function(a,b){var c,d="Face";return a=a.ucfirst()+d,c=FlipClock[a]?new FlipClock[a](this,b):new FlipClock[this.defaultClockFace+d](this,b),c.build(),c},loadLanguage:function(a){var b;return b=FlipClock.Lang[a.ucfirst()]?FlipClock.Lang[a.ucfirst()]:FlipClock.Lang[a]?FlipClock.Lang[a]:FlipClock.Lang[this.defaultLanguage]},localize:function(a,b){var c=this.lang;if(!a)return null;var d=a.toLowerCase();return"object"==typeof b&&(c=b),c&&c[d]?c[d]:a},start:function(a){var b=this;b.running||b.countdown&&!(b.countdown&&b.time.time>0)?b.log("Trying to start timer when countdown already at 0"):(b.face.start(b.time),b.timer.start(function(){b.flip(),"function"==typeof a&&a()}))},stop:function(a){this.face.stop(),this.timer.stop(a);for(var b in this.lists)this.lists[b].stop()},reset:function(a){this.timer.reset(a),this.face.reset()},setTime:function(a){this.time.time=a,this.face.setTime(a)},getTime:function(){return this.time},setCountdown:function(a){var b=this.running;this.countdown=a?!0:!1,b&&(this.stop(),this.start())},flip:function(){this.face.flip()}}),FlipClock.Face=FlipClock.Base.extend({dividers:[],factory:!1,lists:[],constructor:function(a,b){this.base(b),this.factory=a,this.dividers=[]},build:function(){},createDivider:function(b,c,d){"boolean"!=typeof c&&c||(d=c,c=b);var e=['<span class="'+this.factory.classes.dot+' top"></span>','<span class="'+this.factory.classes.dot+' bottom"></span>'].join("");d&&(e=""),b=this.factory.localize(b);var f=['<span class="'+this.factory.classes.divider+" "+(c?c:"").toLowerCase()+'">','<span class="'+this.factory.classes.label+'">'+(b?b:"")+"</span>",e,"</span>"];return a(f.join(""))},createList:function(a,b){"object"==typeof a&&(b=a,a=0);var c=new FlipClock.List(this.factory,a,b);return c},reset:function(){},setTime:function(a){this.flip(a)},addDigit:function(a){var b=this.createList(a,{classes:{active:this.factory.classes.active,before:this.factory.classes.before,flip:this.factory.classes.flip}});b.$obj.insertBefore(this.factory.lists[0].$obj),this.factory.lists.unshift(b)},start:function(){},stop:function(){},flip:function(b,c){var d=this;c||(d.factory.countdown?(d.factory.time.time<=0&&d.factory.stop(),d.factory.time.time--):d.factory.time.time++);var e=d.factory.lists.length-b.length;0>e&&(e=0);var f=!1;a.each(b,function(a,b){a+=e;var g=d.factory.lists[a];if(g){var h=g.digit;g.select(b),b==h||c||g.play()}else d.addDigit(b),f=!0});for(var g=0;g<b.length;g++)g>=e&&d.factory.lists[g].digit!=b[g]&&d.factory.lists[g].select(b[g])}}),FlipClock.List=FlipClock.Base.extend({digit:0,classes:{active:"flip-clock-active",before:"flip-clock-before",flip:"flip"},factory:!1,$obj:!1,items:[],constructor:function(a,b){this.factory=a,this.digit=b,this.$obj=this.createList(),b>0&&this.select(b),this.factory.$wrapper.append(this.$obj)},select:function(a){"undefined"==typeof a?a=this.digit:this.digit=a;{var b=this.$obj.find('[data-digit="'+a+'"]');this.$obj.find("."+this.classes.active).removeClass(this.classes.active),this.$obj.find("."+this.classes.before).removeClass(this.classes.before)}this.factory.countdown?b.is(":last-child")?this.$obj.find(":first-child").addClass(this.classes.before):b.next().addClass(this.classes.before):b.is(":first-child")?this.$obj.find(":last-child").addClass(this.classes.before):b.prev().addClass(this.classes.before),b.addClass(this.classes.active)},play:function(){this.$obj.addClass(this.factory.classes.play)},stop:function(){var a=this;setTimeout(function(){a.$obj.removeClass(a.factory.classes.play)},this.factory.timer.interval)},createList:function(){for(var b=a('<ul class="'+this.classes.flip+" "+(this.factory.running?this.factory.classes.play:"")+'" />'),c=0;10>c;c++){var d=a(['<li data-digit="'+c+'">','<a href="#">','<div class="up">','<div class="shadow"></div>','<div class="inn">'+c+"</div>","</div>",'<div class="down">','<div class="shadow"></div>','<div class="inn">'+c+"</div>","</div>","</a>","</li>"].join(""));this.items.push(d),b.append(d)}return b}}),FlipClock.Time=FlipClock.Base.extend({minimumDigits:0,time:0,factory:!1,constructor:function(a,b,c){this.base(c),this.factory=a,b&&(this.time=b)},convertDigitsToArray:function(a){var b=[];a=a.toString();for(var c=0;c<a.length;c++)a[c].match(/^\d*$/g)&&b.push(a[c]);return b},digit:function(a){var b=this.toString(),c=b.length;return b[c-a]?b[c-a]:!1},digitize:function(b){var c=[];return a.each(b,function(a,b){b=b.toString(),1==b.length&&(b="0"+b);for(var d=0;d<b.length;d++)c.push(b[d])}),c.length>this.minimumDigits&&(this.minimumDigits=c.length),this.minimumDigits>c.length&&c.unshift("0"),c},getDayCounter:function(a){var b=[this.getDays(),this.getHours(!0),this.getMinutes(!0)];return a&&b.push(this.getSeconds(!0)),this.digitize(b)},getDays:function(a){var b=this.time/60/60/24;return a&&(b%=7),Math.floor(b)},getHourCounter:function(){var a=this.digitize([this.getHours(),this.getMinutes(!0),this.getSeconds(!0)]);return a},getHourly:function(){return this.getHourCounter()},getHours:function(a){var b=this.time/60/60;return a&&(b%=24),Math.floor(b)},getMilitaryTime:function(){var a=new Date,b=this.digitize([a.getHours(),a.getMinutes(),a.getSeconds()]);return b},getMinutes:function(a){var b=this.time/60;return a&&(b%=60),Math.floor(b)},getMinuteCounter:function(){var a=this.digitize([this.getMinutes(),this.getSeconds(!0)]);return a},getSeconds:function(a){var b=this.time;return a&&(60==b?b=0:b%=60),Math.ceil(b)},getTime:function(){var a=new Date,b=a.getHours(),c=this.digitize([b>12?b-12:0===b?12:b,a.getMinutes(),a.getSeconds()]);return c},getWeeks:function(){var a=this.time/60/60/24/7;return mod&&(a%=52),Math.floor(a)},removeLeadingZeros:function(b,c){var d=0,e=[];return a.each(c,function(a){b>a?d+=parseInt(c[a],10):e.push(c[a])}),0===d?e:c},toString:function(){return this.time.toString()}}),FlipClock.Timer=FlipClock.Base.extend({callbacks:{destroy:!1,create:!1,init:!1,interval:!1,start:!1,stop:!1,reset:!1},count:0,factory:!1,interval:1e3,constructor:function(a,b){this.base(b),this.factory=a,this.callback(this.callbacks.init),this.callback(this.callbacks.create)},getElapsed:function(){return this.count*this.interval},getElapsedTime:function(){return new Date(this.time+this.getElapsed())},reset:function(a){clearInterval(this.timer),this.count=0,this._setInterval(a),this.callback(this.callbacks.reset)},start:function(a){this.factory.running=!0,this._createTimer(a),this.callback(this.callbacks.start)},stop:function(a){this.factory.running=!1,this._clearInterval(a),this.callback(this.callbacks.stop),this.callback(a)},_clearInterval:function(){clearInterval(this.timer)},_createTimer:function(a){this._setInterval(a)},_destroyTimer:function(a){this._clearInterval(),this.timer=!1,this.callback(a),this.callback(this.callbacks.destroy)},_interval:function(a){this.callback(this.callbacks.interval),this.callback(a),this.count++},_setInterval:function(a){var b=this;b.timer=setInterval(function(){b._interval(a)},this.interval)}}),String.prototype.ucfirst=function(){return this.substr(0,1).toUpperCase()+this.substr(1)},a.fn.FlipClock=function(b,c){return"object"==typeof b&&(c=b,b=0),new FlipClock(a(this),b,c)},a.fn.flipClock=function(b,c){return a.fn.FlipClock(b,c)}}(jQuery),function(a){FlipClock.TwentyFourHourClockFace=FlipClock.Face.extend({constructor:function(a,b){a.countdown=!1,this.base(a,b)},build:function(b){var c=this,d=this.factory.$wrapper.find("ul");b=b?b:this.factory.time.time||this.factory.time.getMilitaryTime(),b.length>d.length&&a.each(b,function(a,b){c.factory.lists.push(c.createList(b))}),this.dividers.push(this.createDivider()),this.dividers.push(this.createDivider()),a(this.dividers[0]).insertBefore(this.factory.lists[this.factory.lists.length-2].$obj),a(this.dividers[1]).insertBefore(this.factory.lists[this.factory.lists.length-4].$obj),this._clearExcessDigits(),this.autoStart&&this.start()},flip:function(a){a=a?a:this.factory.time.getMilitaryTime(),this.base(a)},_clearExcessDigits:function(){for(var a=this.factory.lists[this.factory.lists.length-2],b=this.factory.lists[this.factory.lists.length-4],c=6;10>c;c++)a.$obj.find("li:last-child").remove(),b.$obj.find("li:last-child").remove()}})}(jQuery),function(a){FlipClock.CounterFace=FlipClock.Face.extend({autoStart:!1,constructor:function(a,b){a.timer.interval=0,a.autoStart=!1,a.running=!0,a.increment=function(){a.countdown=!1,a.setTime(a.getTime().time+1)},a.decrement=function(){a.countdown=!0,a.setTime(a.getTime().time-1)},a.setValue=function(b){a.setTime(b)},a.setCounter=function(b){a.setTime(b)},this.base(a,b)},build:function(){var b=this,c=this.factory.$wrapper.find("ul"),d=[],e=this.factory.getTime().digitize([this.factory.getTime().time]);e.length>c.length&&a.each(e,function(a,c){var e=b.createList(c);e.select(c),d.push(e)}),a.each(d,function(a,b){b.play()}),this.factory.lists=d},flip:function(a){var b=this.factory.getTime().digitize([this.factory.getTime().time]);this.base(b,a)}})}(jQuery),function(a){FlipClock.DailyCounterFace=FlipClock.Face.extend({showSeconds:!0,constructor:function(a,b){this.base(a,b)},build:function(b,c){var d=this,e=this.factory.$wrapper.find("ul"),f=[],g=0;c=c?c:this.factory.time.getDayCounter(this.showSeconds),c.length>e.length&&a.each(c,function(a,b){f.push(d.createList(b))}),this.factory.lists=f,this.showSeconds?a(this.createDivider("Seconds")).insertBefore(this.factory.lists[this.factory.lists.length-2].$obj):g=2,a(this.createDivider("Minutes")).insertBefore(this.factory.lists[this.factory.lists.length-4+g].$obj),a(this.createDivider("Hours")).insertBefore(this.factory.lists[this.factory.lists.length-6+g].$obj),a(this.createDivider("Days",!0)).insertBefore(this.factory.lists[0].$obj),this._clearExcessDigits(),this.autoStart&&this.start()},flip:function(a,b){b||(b=this.factory.time.getDayCounter(this.showSeconds)),this.base(b,a)},_clearExcessDigits:function(){for(var a=this.factory.lists[this.factory.lists.length-2],b=this.factory.lists[this.factory.lists.length-4],c=6;10>c;c++)a.$obj.find("li:last-child").remove(),b.$obj.find("li:last-child").remove()}})}(jQuery),function(a){FlipClock.HourlyCounterFace=FlipClock.Face.extend({clearExcessDigits:!0,constructor:function(a,b){this.base(a,b)},build:function(b,c){var d=this,e=this.factory.$wrapper.find("ul"),f=[];c=c?c:this.factory.time.getHourCounter(),c.length>e.length&&a.each(c,function(a,b){f.push(d.createList(b))}),this.factory.lists=f,a(this.createDivider("Seconds")).insertBefore(this.factory.lists[this.factory.lists.length-2].$obj),a(this.createDivider("Minutes")).insertBefore(this.factory.lists[this.factory.lists.length-4].$obj),b||a(this.createDivider("Hours",!0)).insertBefore(this.factory.lists[0].$obj),this.clearExcessDigits&&this._clearExcessDigits(),this.autoStart&&this.start()},flip:function(a,b){b||(b=this.factory.time.getHourCounter()),this.base(b,a)},_clearExcessDigits:function(){for(var a=this.factory.lists[this.factory.lists.length-2],b=this.factory.lists[this.factory.lists.length-4],c=6;10>c;c++)a.$obj.find("li:last-child").remove(),b.$obj.find("li:last-child").remove()}})}(jQuery),function(){FlipClock.MinuteCounterFace=FlipClock.HourlyCounterFace.extend({clearExcessDigits:!1,constructor:function(a,b){this.base(a,b)},build:function(){this.base(!0,this.factory.time.getMinuteCounter())},flip:function(a){this.base(a,this.factory.time.getMinuteCounter())}})}(jQuery),function(a){FlipClock.TwelveHourClockFace=FlipClock.TwentyFourHourClockFace.extend({meridium:!1,meridiumText:"AM",build:function(b){b=b?b:this.factory.time.time?this.factory.time.time:this.factory.time.getTime(),this.base(b),this.meridiumText=this._isPM()?"PM":"AM",this.meridium=a(['<ul class="flip-clock-meridium">',"<li>",'<a href="#">'+this.meridiumText+"</a>","</li>","</ul>"].join("")),this.meridium.insertAfter(this.factory.lists[this.factory.lists.length-1].$obj)},flip:function(){this.meridiumText!=this._getMeridium()&&(this.meridiumText=this._getMeridium(),this.meridium.find("a").html(this.meridiumText)),this.base(this.factory.time.getTime())},_getMeridium:function(){return(new Date).getHours()>=12?"PM":"AM"},_isPM:function(){return"PM"==this._getMeridium()?!0:!1},_clearExcessDigits:function(){for(var a=this.factory.lists[this.factory.lists.length-2],b=this.factory.lists[this.factory.lists.length-4],c=6;10>c;c++)a.$obj.find("li:last-child").remove(),b.$obj.find("li:last-child").remove()}})}(jQuery),function(){FlipClock.Lang.German={years:"Jahre",months:"Monate",days:"Tage",hours:"Stunden",minutes:"Minuten",seconds:"Sekunden"},FlipClock.Lang.de=FlipClock.Lang.German,FlipClock.Lang["de-de"]=FlipClock.Lang.German,FlipClock.Lang.german=FlipClock.Lang.German}(jQuery),function(){FlipClock.Lang.English={years:"Years",months:"Months",days:"Days",hours:"Hours",minutes:"Minutes",seconds:"Seconds"},FlipClock.Lang.en=FlipClock.Lang.English,FlipClock.Lang["en-us"]=FlipClock.Lang.English,FlipClock.Lang.english=FlipClock.Lang.English}(jQuery),function(){FlipClock.Lang.Spanish={years:"A&#241;os",months:"Meses",days:"D&#205;as",hours:"Horas",minutes:"Minutos",seconds:"Segundo"},FlipClock.Lang.es=FlipClock.Lang.Spanish,FlipClock.Lang["es-es"]=FlipClock.Lang.Spanish,FlipClock.Lang.spanish=FlipClock.Lang.Spanish}(jQuery),function(){FlipClock.Lang.French={years:"ans",months:"mois",days:"jours",hours:"heures",minutes:"minutes",seconds:"secondes"},FlipClock.Lang.fr=FlipClock.Lang.French,FlipClock.Lang["fr-ca"]=FlipClock.Lang.French,FlipClock.Lang.french=FlipClock.Lang.French}(jQuery);