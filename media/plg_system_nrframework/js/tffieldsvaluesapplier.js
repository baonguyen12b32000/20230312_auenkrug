var TF_Fields_Values_Applier=function(){function e(e,t){this.data=e,this.applying_data=e,this.item=t,this.breakpoints=["desktop","tablet","mobile"],this.dimensions=["top","right","bottom","left"],this.border_radius_dimensions=["top_left","top_right","bottom_right","bottom_left"]}var t=e.prototype;return t.apply=function(){this.data[this.item]&&(this.applying_data=this.data[this.item],this.applyData())},t.applyData=function(){for(var e in this.applying_data){var t=this.applying_data[e];e.startsWith("[")||e.endsWith("]")||(e="["+e+"]"),t.responsive?t.dimensions?this.setDimensionsResponsiveValue(e,t):this.setResponsiveValue(e,t.type,t.value):this.setValue(e,t.type,t.value)}},t.setDimensionsResponsiveValue=function(e,t){var i=t.type,s=t.value,n=t.border_radius,o=n?this.border_radius_dimensions:this.dimensions;if(null!==s&&"object"==typeof s)for(breakpoint in s){if(null!==s[breakpoint]&&"object"!=typeof s[breakpoint]){var a=s[breakpoint];if(s[breakpoint]={},n)for(index in this.border_radius_dimensions)s[breakpoint][this.border_radius_dimensions[index]]=a;else for(index in this.dimensions)s[breakpoint][this.dimensions[index]]=a}for(dimension in s[breakpoint]){var r=e+"["+breakpoint+"]["+dimension+"]";this.setValue(r,i,s[breakpoint][dimension])}}else for(breakpoint in this.breakpoints)for(dimension in o){var p=e+"["+this.breakpoints[breakpoint]+"]["+o[dimension]+"]";this.setValue(p,i,s)}},t.setResponsiveValue=function(e,t,i){if(null!==i&&"object"==typeof i)for(breakpoint in i){var s=e+"["+breakpoint+"]";this.setValue(s,t,i[breakpoint])}else for(breakpoint in this.breakpoints){var n=e+"["+this.breakpoints[breakpoint]+"]";this.setValue(n,t,i)}},t.setValue=function(e,t,i){switch(t){case"text":case"number":case"color":document.querySelector('input[type="'+t+'"][name$="'+e+'"]').value=i;break;case"nrtoggle":var s=document.querySelector('input[type="checkbox"][name$="'+e+'"]');s.checked=i,s.dispatchEvent(new CustomEvent("change",{bubbles:!0}));break;case"list":var n=document.querySelector('select[name$="'+e+'"]');n.value=i,n.classList.contains("hasChosen")&&(jQuery(n).chosen("destroy"),jQuery(n).chosen()),n.dispatchEvent(new CustomEvent("change",{bubbles:!0}))}},e}();

