var _upload={total:0,completed:0,ismin:1};_upload.tips=$("#upload_file_tips"),_upload.el=$("#uploading_file_list"),_upload.filelist=$(".fileList"),_upload.fid=null;var attachextensions=-1!=_explorer.space.attachextensions.indexOf("|")?_explorer.space.attachextensions.join("|"):_explorer.space.attachextensions;function fileupload(e,a){e.off(),e.fileupload({url:_explorer.appUrl+"&do=ajax&operation=uploads&container="+a,dataType:"json",autoUpload:!0,maxChunkSize:parseInt(_explorer.space.maxChunkSize),dropZone:"wangpan-upload-folder"==e.attr("id")?null:$("#middleconMenu"),pasteZone:"wangpan-upload-folder"==e.attr("id")?null:$("#middleconMenu"),maxFileSize:parseInt(_explorer.space.maxattachsize)>0?parseInt(_explorer.space.maxattachsize):null,acceptFileTypes:new RegExp(attachextensions,"i"),sequentialUploads:!0}).on("fileuploadadd",function(e,a){a.context=$('<li class="dialog-file-list"></li>').appendTo(_upload.el),$.each(a.files,function(e,t){$(getItemTpl(t)).appendTo(a.context),uploadadd(),_uploadheight()})}).on("fileuploadsubmit",function(e,a){a.context.find(".upload-cancel").off("click").on("click",function(){a.abort(),a.files="",uploaddone(),$(this).parents(".dialog-info").find(".upload-cancel").hide(),$(this).parents(".dialog-info").find(".upload-file-status").html('<span class="cancel show_uploading_status"><em></em><i>'+__lang.already_cancel+"</i></span>")}),uploadsubmit(),$.each(a.files,function(e,t){t.relativePath=t.relativePath?t.relativePath+t.name:"";var o=t.webkitRelativePath?t.webkitRelativePath:t.relativePath;a.formData={relativePath:o}})}).on("fileuploadprocessalways",function(e,a){var t=a.index,o=a.files[t];o.error&&(uploaddone(),a.context.find(".upload-item.percent").html('<span class="danger" title="'+o.error+'">'+o.error+"</span>"))}).on("fileuploadprogress",function(e,a){a.index;_upload.bitrate=formatSize(a.bitrate/8);var t=parseInt(a.loaded/a.total*100,10);a.context.find(".process").css("width",t+"%"),a.context.find(".upload-file-status .speed").html(_upload.bitrate+"/s"),a.context.find(".upload-file-status .precent").html(t+"%")}).on("fileuploadprogressall",function(e,a){_upload.bitrate=formatSize(a.bitrate/8);var t=parseInt(a.loaded/a.total*100,10);uploadprogress(_upload.bitrate+"/s",t+"%"),_upload.el.find(".panel-heading .upload-progress-mask").css("width",t+"%")}).on("fileuploaddone",function(e,t){uploaddone(),t.context.find(".upload-progress-mask").css("width","0%"),t.context.find(".upload-cancel").hide(),t.context.find(".process").css("width","100%")&&t.context.find(".process").css("background-color","#fff"),$.each(t.result.files,function(e,o){if(o.error){o.relativePath&&o.relativePath;t.context.find(".dialog-info .upload-file-status").html('<span class="danger" title="'+o.error+'">'+o.error+"</span>")}else{if(_upload.tips.find(".dialog-body-text").html(_upload.completed+"/"+_upload.total),t.context.find(".upload-file-status .speed").html(""),t.context.find(".upload-file-operate").html("完成"),o.data.folderarr){for(var l=0;l<o.data.folderarr.length;l++)_explorer.sourcedata.folder[o.data.folderarr[l].fid]=o.data.folderarr[l];var i=jQuery("#position").jstree(!0);i.refresh_node(i.get_selected())}if(o.data.icoarr)for(l=0;l<o.data.icoarr.length;l++)o.data.icoarr[l].pfid==_selectfile.cons["f-"+a].fid&&(_explorer.sourcedata.icos[o.data.icoarr[l].rid]=o.data.icoarr[l],_selectfile.cons["f-"+a].CreateIcos(o.data.icoarr[l]))}})}).on("fileuploadfail",function(e,a){$.each(a.files,function(e,t){uploaddone(),a.context.find(".upload-item.percent").html('<span class="danger" title="'+t.error+'">'+t.error+"</span>")})}).on("fileuploaddrop",function(e,t){var o=e.dataTransfer.getData("text/plain");if(o&&(e.preventDefault(),_explorer.Permission_Container("link",a)))return $.getJSON(_explorer.appUrl+"&do=dzzcp&do=newlink&path="+parseInt(a)+"&handlekey=handle_add_newlink&link="+encodeURIComponent(o),function(e){e?e.error?Alert(e.error,3):(_explorer.sourcedata.icos[e.rid]=e,_selectfile.cons["f-"+a].CreateIcos(e)):Alert(__lang.js_network_error,1)}),!1}).on("fileuploaddragover",function(e){e.dataTransfer.dropEffect="copy",e.preventDefault()})}function uploadadd(){_upload.total++,_upload.tips.show(),_upload.tips.find(".dialog-body-text").html(_upload.completed+"/"+_upload.total)}function getItemTpl(e){var a=e.webkitRelativePath?e.webkitRelativePath:e.relativePath?e.relativePath:e.name;return'<div class="process" style="position:absolute;z-index:-1;height:100%;background-color:#e8f5e9;-webkit-transition:width 0.6s ease;-o-transition:width 0.6s ease;transition:width 0.6s ease;width:0%;"></div> <div class="dialog-info"> <div class="upload-file-name"><div class="dialog-file-icon" align="center">'+('<img src="dzz/images/extimg/'+e.name.split(".").pop()+'.png" onerror="replace_img(this)" style="width:24px;height:24px;position:absolute;left:0;"/>')+'</div> <span class="name-text">'+e.name+'</span> </div> <div class="upload-file-size">'+(e.size?formatSize(e.size):"")+'</div> <div class="upload-file-path"><a title="" class="" href="javascript:;">'+a+'</a> </div> <div class="upload-file-status"> <span class="uploading"><em class="precent"></em><em class="speed">'+__lang.upload_processing+'</em></span> <span class="success"><em></em><i></i></span> </div> <div class="upload-file-operate"> <em class="operate-pause"></em> <em class="operate-continue"></em> <em class="operate-retry"></em> <em class="operate-remove"></em> <a class="error-link upload-cancel" href="javascript:void(0);">'+__lang.cancel+"</a> </div> </div>"}function replace_img(e){jQuery(e).attr("src","dzz/images/default/icodefault.png")}function formatSize(e){var a=-1;do{e/=1024,a++}while(e>99);return Math.max(e,0).toFixed(1)+["kB","MB","GB","TB","PB","EB"][a]}function uploadsubmit(){_upload.el.find(".upload-sum-title").show().html(_upload.completed+"/"+_upload.total)}function uploaddone(){_upload.completed++,_upload.completed>_upload.total?(_upload.el.find(".upload-sum-title").html(__lang.upload_finish+":"),_upload.el.find(".panel").addClass("ismin"),_upload.ismin=1):_upload.el.find(".upload-sum-completed").show().html(_upload.completed+"/"+_upload.total)}function uploadprogress(e,a){_upload.el.find(".upload-speed").show().find(".upload-speed-value").html(e),_upload.speedTimer&&window.clearTimeout(_upload.speedTimer),_upload.speedTimer=window.setTimeout(function(){_upload.el.find(".upload-speed").hide()},2e3)}function _uploadheight(){var e=$("#uploading_file_list").height();e>441&&(_upload.el.animate({scrollTop:e}),_upload.el.css({"overflow-y":"auto","overflow-x":"hidden","max-height":"460px"}))}attachextensions=""==attachextensions?".*$":"(.|/)("+attachextensions+")$",$(document).on("click",".dialog-close",function(){$(this).parent(".dialog-tips").hide()}),$(document).on("click",".dialog-header-close",function(){$(this).parents(".docunment-dialog").hide(),$("#uploading_file_list").html("")}),$(document).off("click.icon").on("click.icon",".dialog-header-narrow",function(){if($(this).hasClass("dzz-dzz-min"))return $(this).removeClass("dzz-dzz-min").addClass("dzz-dzz-max"),$(this).parents(".docunment-dialog").css({"max-height":"146px",animation:"15s"}),!1;$(this).removeClass("dzz-dzz-max").addClass("dzz-dzz-min"),$(this).parents(".docunment-dialog").css({"max-height":"600px",animation:"15s"})});