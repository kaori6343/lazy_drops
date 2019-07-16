<footer id="footer">
   <small>(C) 2019 lazy drop's brother.</small>
</footer>
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script type="text/javascript" src="./footerFixed.js"></script>
<script>

$(function(){

  //画像ライブプレビュー
  var $dropArea = $('.area-drop');
  var $fileInput = $('.input-file');
  $dropArea.on('dragover',function(e){
    e.stopPropagation();
    e.preventDefault();
    $(this).css('border', '3px #ccc dashed');
  });
  $dropArea.on('dragleave', function(e){
    e.stopPropagation();
    e.preventDefault();
    $(this).css('border', 'none');
  });
  $fileInput.on('change', function(e){
    $dropArea.css('border', 'none');
    var file = this.files[0],
    $img = $(this).siblings('.prev-img'),
    fileReader = new FileReader();

    fileReader.onload = function(event){

      $img.attr('src', event.target.result).show();
    };
    fileReader.readAsDataURL(file);

  });

  //画像モーダル画面
  $(".img-list").click(function(){
    $("#graydisplay").html($(this).prop('outerHTML'));
    $("#graydisplay").fadeIn(200);
  });
  $("#graydisplay, #graydisplay img").click(function(){
    $("#graydisplay").fadeOut(200);
  });
});

</script>

<!-- <script>
$(function(){
  const MSG01 = '20文字以内で入力してください。';
  const MSG02 = '入力必須です。';
  const MSG03 = 'emailの形式ではありません。';
  const MSG04 = '200文字以内で入力してください。';
  const MSG05 = '7文字で入力してください。';

  $('.valid-text').keyup(function(){

  var form_g = $(this).closest('.form-group');

  if($(this).val().length > 20 ){
    form_g.removeClass('has-success').addClass('has-error');
    form_g.find('.help-block').text(MSG01);
  }else{
    form_g.removeClass('has-error').addClass('has-success');
    form_g.find('.help-block').text('');
  }
});
$('.valid-number').change(function(){

  var form_g = $(this).closest('.form-group');

  var format = $(this).val().replace(/[Ａ-Ｚａ-ｚ０-９]/g,function(s){ return String.fromCharCode(s.charCodeAt(0)-0xFEE0) });

  if(format.length === 7){
    $(this).val(format);
    form_g.removeClass('has-error').addClass('has-success');
    form_g.find('.help-block').text('');
  }else{
    $(this).val(format);
    form_g.removeClass('has-success').addClass('has-error');
    form_g.find('.help-block').text(MSG05);
  }
});

$('.valid-email').keyup(function(){

var form_g = $(this).closest('.form-group');

if($(this).val().length === 0 ){
  form_g.removeClass('has-success').addClass('has-error');
  form_g.find('.help-block').text(MSG02);
}else if ($(this).val().length > 50 || !$(this).val().match(/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/) ) {
  form_g.removeClass('has-success').addClass('has-error');
  form_g.find('.help-block').text(MSG03);
}else{
  form_g.removeClass('has-error').addClass('has-success');
  form_g.find('.help-block').text('');
}
});

$('.valid-textarea').keyup(function(){

  var form_g = $(this).closest('.form-group');

  if($(this).val().length === 0 ){
    form_g.removeClass('has-success').addClass('has-error');
    form_g.find('.help-block').text(MSG02);
  }else if ($(this).val().length > 200 ) {
    form_g.removeClass('has-success').addClass('has-error');
    form_g.find('.help-block').text(MSG04);
  }else{
    form_g.removeClass('has-error').addClass('has-success');
    form_g.find('.help-block').text('');
  }
});

});
</script> -->
</body>
</html>
