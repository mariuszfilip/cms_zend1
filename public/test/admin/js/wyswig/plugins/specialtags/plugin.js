CKEDITOR.plugins.add('specialtags',   //name of our plugin
{    
  requires: ['dialog'], //requires a dialog window
  init:function(a) {
  var b="specialtags";
  var c=a.addCommand(b,new CKEDITOR.dialogCommand(b));
  c.modes={wysiwyg:1,source:1}; //Enable our plugin in both modes
  c.canUndo=true;
  //add new button to the editor
  a.ui.addButton("specialtags",
  {
   label:'Dodaj tag',
   command:b,
   icon:this.path+"plus.png"   //path of the icon
  });
  CKEDITOR.dialog.add(b,this.path+"dialog.js") //path of our dialog file
 }
});