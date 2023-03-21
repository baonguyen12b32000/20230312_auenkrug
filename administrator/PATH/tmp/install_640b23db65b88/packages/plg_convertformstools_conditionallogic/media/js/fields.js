function _extends(){return(_extends=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var r,a=arguments[t];for(r in a)Object.prototype.hasOwnProperty.call(a,r)&&(e[r]=a[r])}return e}).apply(this,arguments)}!function(t){"use strict";var a;function r(e){var t,r;e.target.classList.contains("cf-input")&&(t=e.target.closest(".convertforms.cf"))&&(e=e.target.closest(".cf-control-group"))&&(e=e.dataset.key)&&(r=function(e){var t,e=a[e];if(e)return t=[],Object.values(e).forEach(function(e){Object.values(e.rules).forEach(function(e){Object.values(e).forEach(function(e){e.field&&!t.includes(e.field)&&t.push(e.field)})})}),t}(t.dataset.id))&&r.includes(e)&&s(t)}function s(e){var t=e.dataset.id;a.hasOwnProperty(t)&&new o(e,a[t])}var o=function(){function e(e,t){this.form=e,this.options=t,this.run()}var t=e.prototype;return t.run=function(){var r=this;this.log("Start"),Object.values(this.options).forEach(function(e){var t;e.rules&&e.actions&&(r.log("Condition started",e),(t=r.passSome(Object.values(e.rules)))?r.runActions(Object.values(e.actions)):r.runActions(Object.values(e.else||{})),r.log("Condition End",t?"Pass":"Doesn't pass"))}),this.log("End")},t.passSome=function(e){var t=this;return e.some(function(e){return t.passRules(Object.values(e))})},t.passRules=function(e){var t=this;return e.every(function(e){return t.pass(e)})},t.pass=function(e){var t=this.getFieldValue(e.field),r=(e.arg=e.arg||"",!1);switch(e.comparator){case"selected":case"not_selected":case"contains":case"not_contains":r=(t=this.getTextToLowerCase(t)).includes(e.arg.toLowerCase());break;case"starts_with":case"not_start_swith":r=(t=this.getTextToLowerCase(t)).startsWith(e.arg.toLowerCase());break;case"ends_with":case"not_ends_with":r=(t=this.getTextToLowerCase(t)).endsWith(e.arg.toLowerCase());break;case"is_checked":case"not_checked":r=1==t;break;case"regex":case"not_regex":var a="",s=e.arg,o=s.lastIndexOf("#");-1<o&&(a=s.substring(o+1))&&(s=s.substring(0,o)),r=new RegExp(s,a).test(t);break;case"less_than":r=parseFloat(t)<parseFloat(e.arg);break;case"less_equals":r=parseFloat(t)<=parseFloat(e.arg);break;case"greater_than":r=parseFloat(t)>parseFloat(e.arg);break;case"greater_equals":r=parseFloat(t)>=parseFloat(e.arg);break;case"empty":case"not_empty":r=""==t;break;case"total_checked_equals":r=t.length==parseInt(e.arg);break;case"total_checked_not_equals":r=t.length!==parseInt(e.arg);break;case"total_checked_less_than":r=t.length<parseInt(e.arg);break;case"total_checked_less_equals":r=t.length<=parseInt(e.arg);break;case"total_checked_greater_than":r=t.length>parseInt(e.arg);break;case"total_checked_greater_equals":r=t.length>=parseInt(e.arg);break;default:r=this.getTextToLowerCase(t)==this.getTextToLowerCase(e.arg)}return r=e.comparator.startsWith("not_")?!r:r,this.log("Rule",e,t||null,r),r},t.runActions=function(e){var t=this;e&&e.forEach(function(e){t.action(e)})},t.action=function(t){var r=this,e=(t.arg=t.arg||"",this.getFieldGroup(t.field));if(e){switch("select_option"==t.trigger&&"dropdown"==e.dataset.type&&(t.trigger="change_value"),t.trigger){case"show_field":if(!e.classList.contains("cf-hide"))return;e.classList.remove("cf-hide"),e.classList.remove("cf-ignore"),"submit"==e.dataset.type&&(e.querySelector("button").disabled=!1),void 0!==e.dataset.required&&((a=_extends({},t)).trigger="set_required",this.action(a));break;case"hide_field":if(e.classList.contains("cf-hide"))return;e.classList.add("cf-hide"),e.classList.add("cf-ignore"),"submit"==e.dataset.type&&(e.querySelector("button").disabled=!0);var a=_extends({},t);a.trigger="set_optional",this.action(a);break;case"change_value":o=this.getField(t.field),ConvertForms.Helper.setValue(o,t.arg);break;case"select_option":case"deselect_option":o=e.querySelector('input[value="'+t.arg+'"]'),ConvertForms.Helper.toggleCheck(o,"select_option"==t.trigger);break;case"set_required":case"set_optional":var s="set_required"==t.trigger;e.querySelectorAll(".cf-control-input .cf-input").forEach(function(e){e.required=s}),e.dataset.requiredOverride=s;break;case"hide_all_options":case"show_all_options":a="dropdown"==e.dataset.type?".cf-control-input .cf-input option":".cf-control-input .cf-input";e.querySelectorAll(a).forEach(function(e){r.action({field:t.field,trigger:t.trigger.startsWith("hide")?"hide_option":"show_option",arg:e.value})});break;case"hide_option":case"show_option":switch(e.dataset.type){case"radio":case"checkbox":var o=e.querySelector('input[value="'+t.arg+'"]');"show_option"==t.trigger?(o.closest("div").classList.remove("cf-hide"),o.disabled=!1):(o.closest("div").classList.add("cf-hide"),o.disabled=!0,o.checked&&((i=_extends({},t)).trigger="deselect_option",this.action(i)));break;case"dropdown":var i=e.querySelector('option[value="'+t.arg+'"]');"show_option"==t.trigger?(i.disabled=!1,i.classList.remove("cf-hide")):(i.disabled=!0,i.selected=!1,i.classList.add("cf-hide"))}}this.log("Action",t)}},t.inputRequired=function(e,t){t&&!e.required?e.required=!0:e.required&&(e.required=!1)},t.getFieldGroup=function(e){return this.form.querySelector('[data-key="'+e+'"]')},t.getField=function(e){e=this.getFieldGroup(e);return e?e.querySelector(".cf-control-input .cf-input"):""},t.getFieldValue=function(e){var t=this.getField(e);if(!t)return"";var r=[];switch(t.type){case"checkbox":case"radio":return this.getFieldGroup(e).querySelectorAll('input[type="'+t.type+'"]:checked').forEach(function(e){r.push(e.value)}),r;default:return t.value}},t.getTextToLowerCase=function(e){return"string"==typeof e?e.toLowerCase().trim():"object"==typeof e?e.toString().toLowerCase().trim():e},t.log=function(){if(ConvertForms.Helper.getOption("debug",!1)){for(var e,t=arguments.length,r=new Array(t),a=0;a<t;a++)r[a]=arguments[a];(e=console).log.apply(e,["Convert Forms - Form #"+this.form.dataset.id+" - Conditional Logic -"].concat(r))}},e}();ConvertForms.Helper.onReady(function(e){(a=ConvertForms.Helper.getOption("conditional_logic"))&&(t.addEventListener("change",function(e){r(e)}),e.forEach(function(e){s(e)}))})}(document);

