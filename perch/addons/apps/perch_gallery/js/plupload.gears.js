(function(){if(window.google&&google.gears){return}var a=null;if(typeof GearsFactory!="undefined"){a=new GearsFactory()}else{try{a=new ActiveXObject("Gears.Factory");if(a.getBuildInfo().indexOf("ie_mobile")!=-1){a.privateSetGlobalObject(this)}}catch(b){if((typeof navigator.mimeTypes!="undefined")&&navigator.mimeTypes["application/x-googlegears"]){a=document.createElement("object");a.style.display="none";a.width=0;a.height=0;a.type="application/x-googlegears";document.documentElement.appendChild(a)}}}if(!a){return}if(!window.google){window.google={}}if(!google.gears){google.gears={factory:a}}})();(function(e,b,c,d){var f={};function a(h,j,l){var g,i,k,n;i=google.gears.factory.create("beta.canvas");try{i.decode(h);if(!j.width){j.width=i.width}if(!j.height){j.height=i.height}n=Math.min(width/i.width,height/i.height);if(n<1||(n===1&&l==="image/jpeg")){i.resize(Math.round(i.width*n),Math.round(i.height*n));if(j.quality){return i.encode(l,{quality:j.quality/100})}return i.encode(l)}}catch(m){}return h}c.runtimes.Gears=c.addRuntime("gears",{getFeatures:function(){return{dragdrop:true,jpgresize:true,pngresize:true,chunks:true,progress:true,multipart:true,multi_selection:true}},init:function(i,k){var j;if(!e.google||!google.gears){return k({success:false})}try{j=google.gears.factory.create("beta.desktop")}catch(h){return k({success:false})}function g(n){var m,l,o=[],p;for(l=0;l<n.length;l++){m=n[l];p=c.guid();f[p]=m.blob;o.push(new c.File(p,m.name,m.blob.length))}i.trigger("FilesAdded",o)}i.bind("PostInit",function(){var m=i.settings,l=b.getElementById(m.drop_element);if(l){c.addEvent(l,"dragover",function(n){j.setDropEffect(n,"copy");n.preventDefault()},i.id);c.addEvent(l,"drop",function(o){var n=j.getDragData(o,"application/x-gears-files");if(n){g(n.files)}o.preventDefault()},i.id);l=0}c.addEvent(b.getElementById(m.browse_button),"click",function(r){var q=[],o,n,p;r.preventDefault();no_type_restriction:for(o=0;o<m.filters.length;o++){p=m.filters[o].extensions.split(",");for(n=0;n<p.length;n++){if(p[n]==="*"){q=[];break no_type_restriction}q.push("."+p[n])}}j.openFiles(g,{singleFile:!m.multi_selection,filter:q})},i.id)});i.bind("UploadFile",function(r,o){var t=0,s,p,q=0,n=r.settings.resize,l;if(n&&/\.(png|jpg|jpeg)$/i.test(o.name)){f[o.id]=a(f[o.id],n,/\.png$/i.test(o.name)?"image/png":"image/jpeg")}o.size=f[o.id].length;p=r.settings.chunk_size;l=p>0;s=Math.ceil(o.size/p);if(!l){p=o.size;s=1}function m(){var y,A,v=r.settings.multipart,u=0,z={name:o.target_name||o.name},w=r.settings.url;function x(C){var B,H="----pluploadboundary"+c.guid(),E="--",G="\r\n",D,F;if(v){y.setRequestHeader("Content-Type","multipart/form-data; boundary="+H);B=google.gears.factory.create("beta.blobbuilder");c.each(c.extend(z,r.settings.multipart_params),function(J,I){B.append(E+H+G+'Content-Disposition: form-data; name="'+I+'"'+G+G);B.append(J+G)});F=c.mimeTypes[o.name.replace(/^.+\.([^.]+)/,"$1").toLowerCase()]||"application/octet-stream";B.append(E+H+G+'Content-Disposition: form-data; name="'+r.settings.file_data_name+'"; filename="'+o.name+'"'+G+"Content-Type: "+F+G+G);B.append(C);B.append(G+E+H+E+G);D=B.getAsBlob();u=D.length-C.length;C=D}y.send(C)}if(o.status==c.DONE||o.status==c.FAILED||r.state==c.STOPPED){return}if(l){z.chunk=t;z.chunks=s}A=Math.min(p,o.size-(t*p));if(!v){w=c.buildUrl(r.settings.url,z)}y=google.gears.factory.create("beta.httprequest");y.open("POST",w);if(!v){y.setRequestHeader("Content-Disposition",'attachment; filename="'+o.name+'"');y.setRequestHeader("Content-Type","application/octet-stream")}c.each(r.settings.headers,function(C,B){y.setRequestHeader(B,C)});y.upload.onprogress=function(B){o.loaded=q+B.loaded-u;r.trigger("UploadProgress",o)};y.onreadystatechange=function(){var B;if(y.readyState==4){if(y.status==200){B={chunk:t,chunks:s,response:y.responseText,status:y.status};r.trigger("ChunkUploaded",o,B);if(B.cancelled){o.status=c.FAILED;return}q+=A;if(++t>=s){o.status=c.DONE;r.trigger("FileUploaded",o,{response:y.responseText,status:y.status})}else{m()}}else{r.trigger("Error",{code:c.HTTP_ERROR,message:c.translate("HTTP Error."),file:o,chunk:t,chunks:s,status:y.status})}}};if(t<s){x(f[o.id].slice(t*p,A))}}m()});i.bind("Destroy",function(l){var m,n,o={browseButton:l.settings.browse_button,dropElm:l.settings.drop_element};for(m in o){n=b.getElementById(o[m]);if(n){c.removeAllEvents(n,l.id)}}});k({success:true})}})})(window,document,plupload);