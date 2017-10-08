

                <form class="search_box" action="{$form_action}" method="get">
                    <input type="hidden" name="cx" value="{$search_id}">
                    <input type="hidden" name="cof" value="FORID:11">
                    <input class="go" type="image" name="sa" alt="GO" value="Search" src="/img/search_box/go.png">
                    <input class="query" type="text" name="q" value="{$smarty.get.q}">
                </form>
