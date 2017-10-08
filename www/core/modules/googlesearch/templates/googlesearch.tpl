

    <div class="text">

        {if $page}
            {$page->content}
        {/if}

        <!-- Google Search Result Snippet Begins -->
        <div id="results_{$search_id}"></div>
            <script type="text/javascript">
                var googleSearchIframeName = "results_{$search_id}";
                var googleSearchFormName = "searchbox_{$search_id}";
                var googleSearchFrameWidth = 600;
                var googleSearchFrameborder = 0;
                var googleSearchDomain = "www.google.com";
                var googleSearchPath = "/cse";
            </script>
        <script type="text/javascript" src="http://www.google.com/afsonline/show_afs_search.js"></script>
        <!-- Google Search Result Snippet Ends -->

    </div>
