<?php

namespace app\core;

class Pagination
{
    public static function get_offset($limit, $page_num = 1)
    {
        $limit = (int) $limit;
        $page_num = isset($_GET['page']) ? (int) $_GET['page'] : $page_num;
        $page_num = $page_num < 1 ? 1 : $page_num;
        $offset = ($page_num - 1) * $limit;

        return [$offset, $page_num];
    }

    public static function generate($number)
    {
        $number = (int)$number;
        $query_string = str_replace("url=", "", $_SERVER['QUERY_STRING']);


        $current_link = URLROOT . $query_string;
        // add page if not existing
        if (!strstr($query_string, "page=")) {
            $current_link .= "&page=1";
        }

        return preg_replace("/page=[^&?=]+/", "page=" . $number, $current_link);
    }

    public static function links()
    {
        $links = (object) [];

        $links->prev = "";
        $links->next = "";
        $query_string = str_replace("url=", "", $_SERVER['QUERY_STRING']);


        $page_number = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $page_number = $page_number < 1 ? 1 : $page_number;

        $next_page = $page_number + 1;
        $prev_page = ($page_number > 1) ? $page_number - 1 : 1;

        $current_link = URLROOT . $query_string;

        //? add page if not existing
        if (!strstr($query_string, "page=")) {
            $current_link .= "&page=1";
        }

        $links->prev = preg_replace("/page=[^&?=]+/", "page=" . $prev_page, $current_link);
        $links->next = preg_replace("/page=[^&?=]+/", "page=" . $next_page, $current_link);
        $links->current = $page_number;

        return $links;
    }

    public static function show_link()
    {
        $numberOfLinks = 3;
        $max = self::links()->current + $numberOfLinks;
        $cur = self::links()->current;
?>

<center>
    <div class="pager_wrapper gc_blog_pagination">
        <ul class="pagination">
            <li><a href="<?= self::links()->prev ?>">Prev.</a></li>
            <?php for ($i = $cur; $i < $max; $i++) : ?>
            <li class="<?= (self::links()->current == $i) ? 'active' : '' ?>"><a
                    href="<?= self::generate($i) ?>"><?= $i ?></a></li>
            <?php endfor; ?>
            <li><a href="<?= self::links()->next ?>">Next</a></li>
        </ul>
    </div>
</center>
<?php }
}