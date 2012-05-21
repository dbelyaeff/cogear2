$.fn.getChildren = function($class){
    $l = this.attr('data-level');
    if(!$class){
        $class = '.'+this.attr('class');
    }
    $next = this.nextAll($class);
    $childs = [];
    if($next.length){
        $stop = false;
        $next.each(function($key,$item){
            if($($item).attr('data-level') <= $l){
                $stop = true;
            }
            if(!$stop && $($item).attr('data-level') > $l){
                $childs.push($item);
            }
        })
    }
    return $childs;
}