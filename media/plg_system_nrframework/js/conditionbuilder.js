var TF_Condition_Builder=function(){function e(e){this.app_ajax_url="?option=com_ajax&format=raw&plugin=nrframework&task=ConditionBuilder",this.wrapper=e,this.isJ4=Joomla.Modal,this.root_url=this.wrapper.dataset.root,this.site_url=this.root_url.replace("/administrator",""),this.token=this.wrapper.dataset.token,this.init()}var t=e.prototype;return t.init=function(){this.initEvents(),this.initLoadConditions()},t.initEvents=function(){this.prepare(),document.addEventListener("click",function(e){this.addConditionEvent(e),this.deleteConditionEvent(e),this.deleteGroupConditionEvent(e)}.bind(this)),document.addEventListener("change",function(e){this.handleConditionSelector(e)}.bind(this)),jQuery(document).on("change",".condition_selector",function(e){this.handleConditionSelector(e)}.bind(this)),document.addEventListener("afterConditionSettings",function(e){this.loadConditionAssets(e.detail.condition_name,e.detail.element)}.bind(this))},t.prepare=function(){NRHelper.loadStyleSheet(this.site_url+"/media/plg_system_nrframework/css/toggle.css")},t.initLoadConditions=function(){var e=this.wrapper.previousElementSibling.value;if(e){var t={data:e,name:this.wrapper.previousElementSibling.getAttribute("name"),include_rules:this.wrapper.dataset.includeRules,exclude_rules:this.wrapper.dataset.excludeRules},i=this;this.call("init_load",t,function(e){i.wrapper.querySelector(".cb-groups").innerHTML=e,i.getValidRules().forEach(function(e){i.loadConditionAssets(e.value,e.closest(".cb-item"))})})}else this.wrapper.querySelector(".tf-cb-add-new-group").click()},t.loadConditionAssets=function(e,t){var i=this;switch(e){case"Joomla\\UserGroup":case"Joomla\\Menu":case"Component\\ContentCategory":case"Component\\K2Category":case"Component\\VirtueMartCategory":case"Component\\HikashopCategory":NRHelper.loadStyleSheet(this.site_url+"/media/plg_system_nrframework/css/treeselect.css"),NRHelper.loadScript(this.site_url+"/media/plg_system_nrframework/js/treeselect.js",function(){NRTreeselect.init(t)},!0);break;case"Date\\Date":NRHelper.loadStyleSheet(this.site_url+"/media/system/css/fields/calendar.css"),NRHelper.loadScript(this.site_url+"/media/system/js/fields/calendar-locales/en.js"),NRHelper.loadScript(this.site_url+"/media/system/js/fields/calendar-locales/date/gregorian/date-helper.min.js"),NRHelper.loadScript(this.site_url+"/media/system/js/fields/calendar.min.js",function(){t.querySelectorAll(".field-calendar").forEach(function(e){JoomlaCalendar.init(e)})},!0);break;case"Date\\Time":NRHelper.loadStyleSheet(this.site_url+"/media/plg_system_nrframework/css/vendor/jquery-clockpicker.min.css"),NRHelper.loadScript(this.site_url+"/media/plg_system_nrframework/js/vendor/jquery-clockpicker.min.js",function(){t.querySelectorAll(".clockpicker").forEach(function(e){jQuery(e).clockpicker()})},!0);break;case"URL":case"Joomla\\UserID":case"Geo\\City":case"Geo\\Region":case"Referrer":case"IP":case"Component\\VirtueMartCartContainsProducts":case"Component\\HikashopCartContainsProducts":this.isJ4?NRHelper.loadScript(this.site_url+"/media/system/js/fields/joomla-field-subform.js"):NRHelper.loadScript(this.site_url+"/media/jui/js/jquery.ui.core.min.js",function(){NRHelper.loadScript(i.site_url+"/media/jui/js/jquery.ui.sortable.min.js",function(){NRHelper.loadScript(i.site_url+"/media/system/js/subform-repeatable.js")})}),NRHelper.loadStyleSheet(this.site_url+"/media/plg_system_nrframework/css/tfinputrepeater.css"),NRHelper.loadScript(this.site_url+"/media/plg_system_nrframework/js/tfinputrepeater.js");case"Joomla\\UserID":this.isJ4?NRHelper.loadScript(this.site_url+"/media/system/js/fields/joomla-field-user.min.js"):NRHelper.loadScript(this.site_url+"/media/jui/js/fielduser.min.js");case"Component\\VirtueMartCartContainsProducts":case"Component\\HikashopCartContainsProducts":this.loadSelect2();break;case"Component\\K2Item":case"Component\\ContentArticle":case"Component\\VirtueMartSingle":case"Component\\HikashopSingle":this.loadSelect2()}NRHelper.loadStyleSheet(this.site_url+"/media/plg_system_nrframework/css/toggle.css"),"Date"!=e&&jQuery(document).trigger("subform-row-add",[t]),this.isJ4&&this.fixShowOnElements(t)},t.loadSelect2=function(){var e=this;NRHelper.loadStyleSheet(this.site_url+"/media/plg_system_nrframework/css/select2.css"),NRHelper.loadScript(this.site_url+"/media/plg_system_nrframework/js/vendor/select2.min.js",function(){NRHelper.loadScript(e.site_url+"/media/plg_system_nrframework/js/ajaxify.js",!1,!0)},!0)},t.fixShowOnElements=function(t){t.querySelectorAll("[data-showon]").forEach(function(e){e.removeAttribute("data-showon-initialised"),Joomla.Showon.initialise(t)})},t.handleConditionSelector=function(e){var t=e.target.closest(".condition_selector");if(t&&t.value){e.preventDefault();var i=t.closest(".cb-item");i.classList.add("ajax-loading");this.loadConditionSettings(i,t.value,function(){i.classList.remove("ajax-loading"),i.querySelector(".cb-item-content").querySelectorAll("select.hasChosen").forEach(function(e){jQuery(e).chosen("destroy"),jQuery(e).chosen({disable_search_threshold:10,inherit_select_classes:!0})})})}},t.loadConditionSettings=function(i,o,r){var e=parseInt(i.closest(".cb-group").dataset.key),t=parseInt(i.closest(".cb-item").dataset.key),n={conditionItemGroup:this.wrapper.previousElementSibling.getAttribute("name")+"["+e+"][rules]["+t+"]",name:o,request_option:this.wrapper.dataset.option,request_layout:this.wrapper.dataset.layout};this.call("options",n,function(e){e=""!==e?e:'<div class="select-condition-message">'+Joomla.JText._("NR_CB_SELECT_CONDITION_GET_STARTED")+"</div>",i.querySelector(".cb-item-content").innerHTML=e;var t=new CustomEvent("afterConditionSettings",{detail:{element:i,condition_name:o}});document.dispatchEvent(t),r&&r()})},t.deleteGroupConditionEvent=function(e){if(e.target.closest(".removeGroupCondition")){e.preventDefault();var t=e.target.closest(".cb-group");this.getValidRules(t).length&&!confirm(Joomla.JText._("NR_ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ITEM"))||(1==this.getTotalConditionGroups()?(t.querySelectorAll(".cb-item:not(:first-child)").forEach(function(e){e.remove()}),this.resetCondition(t.querySelector(".cb-item"))):t.remove())}},t.deleteConditionEvent=function(e){if(e.target.closest(".tf-cb-remove-condition")){e.preventDefault();var t=e.target.closest(".cb-item");0!==t.querySelector(".condition_selector").selectedIndex&&!confirm(Joomla.JText._("NR_ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ITEM"))||(1!=this.getTotalConditionItems()?this.deleteCondition(t):this.resetCondition(t))}},t.deleteCondition=function(e){var t=e.closest(".cb-group");e.remove(),0==t.querySelectorAll(".cb-item").length&&t.remove()},t.resetCondition=function(e){e.querySelector(".condition_selector").selectedIndex=0,jQuery(e.querySelector(".condition_selector")).chosen("destroy"),jQuery(e.querySelector(".condition_selector")).chosen({disable_search_threshold:10,inherit_select_classes:!0}),jQuery(e.querySelector(".condition_selector")).trigger("change")},t.getTotalConditionGroups=function(){return this.wrapper.querySelectorAll(".cb-group").length},t.getValidRules=function(e){void 0===e&&(e=this.wrapper);var t=e.querySelectorAll("select.condition_selector"),i=[];return t.forEach(function(e){0!==e.selectedIndex&&i.push(e)}),i},t.getTotalConditionItems=function(){return this.wrapper.querySelectorAll(".cb-item").length},t.addConditionEvent=function(e){var t=e.target.closest(".tf-cb-add-new-group");if(t){e.preventDefault();var i=t.closest(".cb-item")||t,o=i.closest(".cb-group"),r=groupKey=0;r=this.addingNewGroup(i)?(groupKey=this.findHighestGroupKey()+1,0):(groupKey=parseInt(o.dataset.key),this.findHighestGroupItemKey(o)+1),i.classList.add("ajax-loading");this.addCondition(i,this.wrapper.previousElementSibling.getAttribute("name"),groupKey,r,function(){i.classList.remove("ajax-loading")})}},t.findHighestGroupKey=function(){return Math.max.apply(Math,Array.from(this.wrapper.querySelectorAll(".cb-group[data-key]")).map(function(e){return parseInt(e.dataset.key)}))},t.findHighestGroupItemKey=function(e){return Math.max.apply(Math,Array.from(e.querySelectorAll(".cb-item[data-key]")).map(function(e){return parseInt(e.dataset.key)}))},t.addCondition=function(o,e,t,r,n){var s=this.addingNewGroup(o),a={conditionItemGroup:e,groupKey:t,conditionKey:r,include_rules:this.wrapper.dataset.includeRules,exclude_rules:this.wrapper.dataset.excludeRules,addingNewGroup:s},l=this;this.call("add",a,function(e){if(s){var t=document.createElement("div");t.innerHTML=e,l.wrapper.querySelector(".cb-groups").insertAdjacentHTML("beforeend",t.innerHTML),l.wrapper.setAttribute("data-max-index",a.groupKey)}else{var i=document.createElement("div");i.innerHTML=e,o.closest(".item-group-footer")?o.closest(".cb-group").querySelector(".cb-items").insertAdjacentHTML("beforeend",i.innerHTML):o.insertAdjacentHTML("afterend",i.innerHTML),o.closest(".cb-group").setAttribute("data-max-index",r)}n&&n()})},t.addingNewGroup=function(e){return!!e&&!e.closest(".cb-group")},t.call=function(e,t,i){var o=this,r=this.root_url+this.app_ajax_url+"&"+this.token+"=1";t.subtask=e,fetch(r,{method:"post",body:JSON.stringify(t)}).then(function(e){return e.text()}).then(function(e){i(e),o.wrapper.querySelectorAll("select.hasChosen").forEach(function(e){jQuery(e).chosen("destroy"),jQuery(e).chosen({disable_search_threshold:10,inherit_select_classes:!0})}),o.wrapper.querySelectorAll(".hasPopover").forEach(function(e){jQuery(e).popover({html:!0,trigger:"hover focus",container:"body"})})}).catch(function(e){alert(e)})},e}(),TF_Condition_Builder_Loader=function(){function e(){this.init()}return e.prototype.init=function(){!function(){if(window.IntersectionObserver){var t=new IntersectionObserver(function(e,t){e.forEach(function(e){e.isIntersecting&&(new TF_Condition_Builder(e.target),t.unobserve(e.target))})},{rootMargin:"0px 0px 0px 0px"});document.querySelectorAll("div.cb").forEach(function(e){t.observe(e)})}}()},e}();!function(){"use strict";document.addEventListener("DOMContentLoaded",function(){new TF_Condition_Builder_Loader})}(window);
