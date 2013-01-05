var DDList = function(options){
    this.init(options);
}
DDList.prototype = {
    container: null,
    items: [],
    options: {
        items: 'dd-item-container',
        placeholder: 'dd-placeholder',
        transport: 'dd-item-transport'
    },
    init: function(container,options){
        $this = this;
        this.container = $(container)
        if(options) this.options = $.extend(this.options,options);
        this.container.sortable({
            placeholder: this.options.placeholder,
            start: function(event,ui){
                $item = $(ui.item[0]);
                $childs = $item.nextAll('[data-thread^="'+$item.attr('data-thread')+'"]');
                $childs.appendTo($item.find('.'+$this.options.transport));
            },
            update: function(event,ui){
                $item = $(ui.item[0]);
                $childs = $item.find('.'+$this.options.transport+' .'+$this.options.items);
                if($childs.length){
                    $childs.insertAfter($item);
                }
            },
            change: function(event,ui){
            },
            out: function(event,ui){
                event.preventDefault();
            }
        });
    }


}

$.fn.nestedSortable = function(options){
    cogear.ddlist = new DDList($(this),options);
}


$(document).ready(function(){
   $('.dd').nestedSortable();
});
////$(document).ready(function(){
//    $('.dd').sortable({
//        placeholder: 'dd-placeholder',
//        helper: function(event,el){
//            $childrens = el.getChildrens();
//            if($childrens.length){
//                $childrens.appendTo(el.find('.dd-item-transport'));
//            }
//            return el;
//        },
//        out: function(event,ui){
//        //           console.log(ui);
//        },
//        start: function(event,ui){
//            $item = ui.item[0];
//            $('<ul class="dd-transport"></ul>').appendTo($item);
//            $child = $item.nextUntil
//        },
//        stop: function(event,ui){
//            $item = ui.item[0];
//        }
//    });
//
//    $.fn.getChildrens = function(){
//        $this = $(this);
//        $childrens = $this.nextAll('[data-pid='+$this.attr('data-id')+']');
//        if($childrens.length){
//            $childrens.each(function(){
//                $subchildrens = $(this).getChildrens();
//                if($subchildrens.length){
//                    $childrens = $.merge($childrens,$subchildrens);
//                }
//            })
//        }
//        return $childrens;
//    }
//});