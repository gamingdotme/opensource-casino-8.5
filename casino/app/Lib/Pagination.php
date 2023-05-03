<?php


namespace VanguardLTE\Lib;


class Pagination
{
    public static function paging($max, $inter, $cur, $link='', $urlquery='', $pad = 3) {
        $pages = ceil(intval($max) / intval($inter));
        if($pages <= 1){
            return '';
        }
        $res = array("<nav aria-label=\"Page navigation example mt-5\"><ul class=\"pagination justify-content-center\">");
        $cur--;
        if ($pages > 8) {
            for ($i=0; $i<$pages; $i++) {
                if ($i === 0) {
                    $res[] = "<li ".(($i === $cur)?"class='page-item active'":"")." data-page='".$i."'><a class=\"page-link\" href='".$link.$urlquery."=".($i+1)."'>‹‹</a></li>";
                } elseif ($i+1 == $pages) {
                    $res[] = "<li ".(($i === $cur)?"class='page-item active'":"")." data-page='".$i."'><a class=\"page-link\" href='".$link.$urlquery."=".($i+1)."'>››</a></li>";
                }elseif($i > $cur - $pad && $i < $cur + $pad) {
                    $res[] = "<li ".(($i === $cur)?"class='page-item active'":"")." data-page='".$i."'><a class=\"page-link\" href='".$link.$urlquery."=".($i+1)."'>".($i+1)."</a></li>";
                }
            }
        } else {
            for ($i=0; $i<$pages; $i++) {
                $res[] = "<li ".(($i === $cur)?"class='page-item active'":"")." data-page='".$i."'><a class=\"page-link\" href='".$link.$urlquery."=".($i+1)."'>".($i+1)."</a></li>";
            }
        }

        $res[] = "</ul></nav>";
        return implode('',$res);


        /*
						<nav aria-label="Page navigation example mt-5">
							<ul class="pagination justify-content-center">
								<li class="page-item @if($page <= 1) disabled } @endif">
									<a class="page-link"
									   href="@if($page <= 1) # @else ?page={{ $prev }} } @endif">Previous</a>
								</li>

        @for($i = 1; $i <= $totalPages; $i++ )
						<li class="page-item @if($page == $i) active @endif">
									<a class="page-link" href="?page={{ $i }}"> {{ $i }} </a>
								</li>
        @endfor

							<li class="page-item @if($page >= $totalPages) disabled @endif">
									<a class="page-link"
									   href="@if($page >= $totalPages) # @else ?page={{ $next }} @endif">Next</a>
								</li>
							</ul>
						</nav>
        */
    }


}