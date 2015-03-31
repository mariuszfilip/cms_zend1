CKEDITOR.dialog.add("tag",function(e){

 var t1 = '{first_name}';
 var t2 = '{last_name}';
 var t3 = '{email}';
 var t4 = '{birth}';
 var t5 = '{address}';
 var t6 = '{phone}';
 var w1 = '{{first_name}}';

 return {
  title:'Personalizuj',
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
  {  id:"info",
    name:'info',
    label:'Tab',
    elements:[

     {
      id : 'format',
      type : 'select',
      label : 'Wybierz',
      accessKey : 'T',
      items :
      [
       [ t1 ],
       [ t2 ],
       [ t3 ],
       [ t4 ],
       [ t5 ],
       [ t6 ],
       [ w1 ]
      ]
     },
     {  
      type:'html',
      html:'<span style="">'+'Wybierz pole do personalizacji'+'</span>'
     }
    ]
  }
  ],
  buttons:[{
   type:'button',
   id:'okBtnPersonal',
   label: 'Wstaw',
   onClick: function(){
      addCode(); //function for adding time to the source
   }
  }, CKEDITOR.dialog.cancelButton],
};

 //function for adding time to the source
 function addCode(){

  //get the value of 'format' field in the 'info' tab of the dialog box
  var t = dialog.getValueOf('info', 'format');
  if(t.length == 0){
   alert('Proszę wybierz pole.')
   return false;
  }

  var myEditor = CKEDITOR.instances.html;
  myEditor.insertHtml(t);

  return false;

 };

});