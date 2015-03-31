CKEDITOR.dialog.add("specialtags",function(e){

 var t1 = '&lt;PREVIEW_MAIL&gt;';
 var t2 = '&lt;UNSUBSCRIBE_NEWSLLETER&gt;';
 var t3 = '&lt;SUBSCRIBER_EDIT&gt;';
 var t4 = '&lt;RECOMMEND_TO_FRIEND&gt;';


 return {
  title:'Tagi',
  resizable : CKEDITOR.DIALOG_RESIZE_BOTH,
  minWidth:300,
  minHeight:100,
  onShow:function(){ 
  },
  onLoad:function(){ 
    dialog = this; 
    this.setupContent();
  },
  onOk:function(){
  },
  contents:[
  {  id:"tags",
    name:'tags',
    label:'Tagi',
    elements:[

     {
      id : 'format_tags',
      type : 'select',
      label : 'Wybierz',
      accessKey : 'T',
      items :
      [
       [ t1 ],
       [ t2 ],
       [ t3 ],
       [ t4 ]
      ]
     },
     {  
      type:'html',
      html:'<span style="">'+'Wybierz link'+'</span>'
     }
    ]
  }
  ],
  buttons:[{
   type:'button',
   id:'okBtn',
   label: 'Wstaw',
   onClick: function(){
      addCode();
   }
  }, CKEDITOR.dialog.cancelButton],
};


 function addCode(){
  var t = dialog.getValueOf('tags', 'format_tags');
  if(t.length == 0){
   alert('Proszę wybierz link.')
   return false;
  }
  var myEditors = CKEDITOR.instances.html;
  myEditors.insertHtml(t);
  return false;

 };

});