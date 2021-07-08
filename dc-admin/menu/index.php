<div id="load"></div>
<div class="row">
    <div class="col-lg-5">
        <h5>Добавить пункт меню</h5>
        <div class="form-group">
            <div style="display: inline-flex;">
                <div style="display: inline-block; width: 75px;font-size: 23px; text-align: center;border: 1px solid #ccc;margin-right: 5px;"><i id="fa_icon"></i></div>
                <input type="text" class="form-control" name="icon" id="icon" disabled>
                <span class="btn btn-secondary ml-2" onclick="openFaSelector()">ИКОНКА</span>
                <span class="btn btn-danger ml-1" onclick="delFaSelector()"><i class="fa fa-times"></i></span>
            </div>
            <br>
            <div id="icons"></div>
            <script>
                FASIconVersion = "4.7.0";
                FASiconUrl="../inc/iconpicker/font-awesome-"+FASIconVersion+".json";
                function delFaSelector(){
                    $('#icons').empty();
                    $('#fa_icon').removeAttr('class');
                    $('#icon').val('');
                    $('#icons').css("display", "");
                }
                function openFaSelector(){
                    $.getJSON(FASiconUrl,function(data){
                        //console.log('success');
                        $('#icons').empty();
                            $.each(data, function (i, emp) {
                                $.each(emp, function (key, value) {
                                    $('#icons').append('<div class="ic"><i class="fa ' + value + '"></i></div>');
                                    $('#icons').css("display", "block");
                                });
                            });
                    }).error(function(){
                        console.log('error');
                    });
                }
                $(document).on('click', function(e) {
                    //console.log(e.target);
                    if(e.target.parentElement.classList == 'ic') {
                        $('#fa_icon').removeAttr('class');
                        $('#icon').val(e.target.className);
                        $('#icons').css("display", "");
                        $('#fa_icon').attr('class', e.target.className);
                    }
                });
            </script>
        </div>

        <div class="form-group">
            Наименование:<i class="red">*</i>
            <input id="name" class="form-control" type="text" placeholder="Наименование" maxlength="50" required>
        </div>
        <div class="form-group">
            Наименование (eng):<i class="red">*</i>
            <input id="name_en" class="form-control" type="text" placeholder="Наименование (eng)" maxlength="50" required>
        </div>
        <hr>

        <div class="form-group">
            <label for="Select1">Ссылка на страницу</label>
            <select class="form-control" id="Select1" name="vibor">
                <option value=""></option>
                <?php
                $pages = $DB->get_records("pages", [], 'id DESC');
                foreach ($pages as $page) {?>
                    <option value="pages/<?=$page->alias?>"><?=$page->name?></option>
                <?php } ?>
            </select>
        </div>
        <script>
            $('#Select1').change(function(){
                $('#link').val( $(this).val());
            });
        </script>
        <div class="form-group">
            Ссылка:<i class="red">*</i>
            <input id="link" class="form-control" type="text" placeholder="Ссылка" maxlength="200" required>
        </div>

        <div class="form-group">
            <button type="button" class="btn btn-success" id="submit"><i class="fa fa-plus"></i> Добавить</button>
            <button type="button" class="btn btn-danger" id="reset"><i class="fa fa-minus"></i> Сбросить</button>
        </div>
    <input type="hidden" id="id">
    </div>
    <div class="col-lg-7">
        <div id="nestable-menu">
            <button type="button" class="btn btn-success" data-action="expand-all"><i class="fa fa-plus"></i> Раскрыть</button>
            <button type="button" class="btn btn-danger" data-action="collapse-all"><i class="fa fa-minus"></i> Свернуть</button>
        </div>
        <div class="cf nestable-lists">
            <div class="dd" id="nestable">
<?php

$query = $DB->get_records_sql("select * from ".$CFG->db['prefix']."menu order by sort ");
//dpr(count($query));
//$query = json_decode(json_encode($query),true);
//dpr($query);
$ref   = [];
$items = [];

foreach($query as $data){
    $thisRef = &$ref[$data->id];
    $thisRef['icon'] = $data->icon;
    $thisRef['parent'] = $data->parent;
    $thisRef['name'] = $data->name;
    $thisRef['name_en'] = $data->name_en;
    $thisRef['link'] = $data->link;
    $thisRef['id'] = $data->id;

    if($data->parent == 0) {
        $items[$data->id] = &$thisRef;
    } else {
        $ref[$data->parent]['child'][$data->id] = &$thisRef;
    }
}

function get_menu($items,$class = 'dd-list') {
    $html = "<ol class=\"".$class."\" id=\"menu-id\">";
    foreach($items as $key=>$value) {
        $html.= '<li class="dd-item dd3-item" data-id="'.$value['id'].'" >
                    <div class="dd-handle dd3-handle"><i id="icon_show'.$value['id'].'" class="'.$value['icon'].'"></i></div>
                    <div class="dd3-content"><span id="name_show'.$value['id'].'">'.$value['name'].'</span> 
                        <span class="span-right"><!--<span id="link_show'.$value['id'].'">'.$value['link'].'</span> &nbsp;&nbsp; -->
                            <a class="edit-button" id="'.$value['id'].'" name="'.$value['name'].'" name_en="'.$value['name_en'].'" link="'.$value['link'].'"  icon="'.$value['icon'].'" ><i class="fa fa-pencil"></i></a>
                            <a class="del-button" id="'.$value['id'].'"><i class="fa fa-trash"></i></a></span> 
                    </div>';
        if(array_key_exists('child',$value)) {
            $html .= get_menu($value['child'],'child');
        }
            $html .= "</li>";
    }
    $html .= "</ol>";
    return $html;
}
print get_menu($items);
?>
        </div>
    </div>
    <p></p>
    <input type="hidden" id="nestable-output">
    <!--<button id="save">Save</button>-->
    </div>
</div>
<script src="./js/jquery.nestable.js"></script>
<script>
$(document).ready(function(){
    var updateOutput = function(e){
        var list   = e.length ? e : $(e.target),
            output = list.data('output');
        if (window.JSON) {
            output.val(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
        } else {
            output.val('JSON browser support required for this demo.');
        }
    };
    // activate Nestable for list 1
    $('#nestable').nestable({
        group: 1
    })
    .on('change', updateOutput);

    // output initial serialised data
    updateOutput($('#nestable').data('output', $('#nestable-output')));
    $('#nestable-menu').on('click', function(e){
        var target = $(e.target),
            action = target.data('action');
        if (action === 'expand-all') {
            $('.dd').nestable('expandAll');
        }
        if (action === 'collapse-all') {
            $('.dd').nestable('collapseAll');
        }
    });
});
</script>

<script>
  $(document).ready(function(){
    $("#load").hide();
    $("#submit").click(function(){
       $("#load").show();

       var dataString = {
           icon : $("#icon").val(),
           name : $("#name").val(),
           name_en : $("#name_en").val(),
           link : $("#link").val(),
           id : $("#id").val()
       };
        $.ajax({
            type: "POST",
            url: "menu/save_menu.php",
            data: dataString,
            dataType: "json",
            cache : false,
            success: function(data){
              if(data.type == 'add'){
                 $("#menu-id").append(data.menu);
              } else if(data.type == 'edit'){
                 $('#name_show'+data.id).html(data.name);
                 $('#name_en_show'+data.id).html(data.name_en);
                 $('#link_show'+data.id).html(data.link);
                 //$('#icon_show'+data.id).html(data.icon);
                 $('#icon_show'+data.id).attr('class', data.icon);
              }
              $('#fa_icon').removeAttr('class');
              $('#icon').val('');
              $('#name').val('');
              $('#name_en').val('');
              $('#link').val('');
              $('#id').val('');
              $("#load").hide();
            } ,error: function(xhr, status, error) {
              alert(error);
            },
        });
    });
    $('.dd').on('change', function() {
        $("#load").show();

          var dataString = {
              data : $("#nestable-output").val(),
            };

        $.ajax({
            type: "POST",
            url: "menu/save.php",
            data: dataString,
            cache : false,
            success: function(data){
              $("#load").hide();
                //alert('Yes');
            } ,error: function(xhr, status, error) {
              alert(error);
            },
        });
    });
    $("#save").click(function(){
         $("#load").show();

          var dataString = {
              data : $("#nestable-output").val(),
            };
        $.ajax({
            type: "POST",
            url: "menu/save.php",
            data: dataString,
            cache : false,
            success: function(data){
              $("#load").hide();
              alert('Data has been saved');

            } ,error: function(xhr, status, error) {
              alert(error);
            },
        });
    });
    $(document).on("click",".del-button",function() {
        var x = confirm('Delete this menu?');
        var id = $(this).attr('id');
        if(x){
            $("#load").show();
             $.ajax({
                type: "POST",
                url: "menu/delete.php",
                data: { id : id },
                cache : false,
                success: function(data){
                  $("#load").hide();
                  $("li[data-id='" + id +"']").remove();
                } ,error: function(xhr, status, error) {
                  alert(error);
                },
            });
        }
    });
    $(document).on("click",".edit-button",function() {
        var id = $(this).attr('id');
        var name = $(this).attr('name');
        var name_en = $(this).attr('name_en');
        var link = $(this).attr('link');
        var icon = $(this).attr('icon');
        $("#id").val(id);
        $("#name").val(name);
        $("#name_en").val(name_en);
        $("#link").val(link);
        $("#icon").val(icon);
        $('#fa_icon').attr('class', icon);
    });
    $(document).on("click","#reset",function() {
        $('#fa_icon').removeAttr('class');
        $('#icon').val('');
        $('#name').val('');
        $('#name_en').val('');
        $('#link').val('');
        $('#id').val('');
    });

  });

</script>






