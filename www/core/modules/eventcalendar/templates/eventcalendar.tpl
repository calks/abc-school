
        <div class="text">
        	<div class="breadcrumbs_share">
            	{$breadcrumbs}
            	{$share}
			</div>

            {if $action != "view_event"}<h1>{$page->title}</h1>{/if}
            {if $action == 'list'}
                {if $page->content != ''}{$page->content}<br><br clear="all">{/if}
                <div>
                    <form action="{$module_url}" method="GET">
                        {$form_events_type->render("event_type")}
                        {$form_events_type->render("region")}
                        <input type="hidden" name="month" value="{$month}">
                        <input type="hidden" name="year" value="{$year}">
                    </form>
                </div>
                <br clear="all">
            {/if}

            <div>
                {if $action == 'list'}
                {$event_list}

                <div align="center" style="margin: 15px 0;">
                    <a href="{$module_url}?create=new">ADD YOUR EVENT</a>
                </div>
                {$page_bottom_block->html_code}
            {/if}
            {if $action == "create"}
                <script type="text/javascript" src="/js/calendar.js"></script>
                <script type="text/javascript" src="/js/calendar-setup.js"></script>
                <script type="text/javascript" src="/js/calendar-en.js"></script>
                <script type="text/javascript" src="/js/scripts.js"></script>

                {if $errors != ""}
                    <div class="error">Error: {$errors}</div>
                {/if}
                <form action="{$module_url}" method="POST" name="event" onsubmit="javascript:return(checkFormEvents(this))">
                    <table summary="" align="left">
                    <tr><td>
                        <table summary="" class="edit" id="eventcalendar_add">
                        <tr><th>Event Name *:</th><td>{$form->render('name')}</td></tr>
                        <tr><th>Kauai Region *:</th><td>{$form->render('region')}</td></tr>
                        <tr><th>Location:</th><td>{$form->render('location')}</td></tr>
                        <tr><th>Comments:</th><td>{$form->render('comments')}</td></tr>
                        <tr><th>Event Date:</th><td class="date_form">{$form->render('date_begin')} (mm/dd/yyyy)</td></tr>
                        <tr><th>Does this event occur every<br> week on the same day?</th><td>{$form->render('re_occurring')} Yes</td></tr>
                        <tr><th><div id="event_fdate_th">Final date of last event:</div></th><td class="date_form">{$form->render('date_end')} (mm/dd/yyyy)</td></tr>
                        <!--tr><th>Start Time:</th><td>{$form->render("time_begin_h")} {$form->render("time_begin_i")}</td></tr>
                        <tr><th>End Time:</th><td>{$form->render("time_end_h")} {$form->render("time_end_i")}</td></tr-->
                        <tr><th>Email Address *:</th><td>{$form->render('email')}</td></tr>
                        <tr><th>Event Type *:</th><td>{$form->render("event_type")}</td></tr>

                        </table>
                        <br>
                        * - required field
                    </td></tr>
                    <tr><td align="right" class="buttom_form">
                        <input type="button" name="save" value="Submit" onclick="if(checkFormEvents(this.form)) this.form.submit();" class="submit">
                        <input type="hidden" name="action" value="{$action}">
                    </td></tr>
                    </table>
                    </form>

                    <br>


                    <script type="text/javascript">
                        {literal}
                            $(function() {
                                $("input[name=date_begin]").datepicker({});
                                $("input[name=date_end]").datepicker({});
                            });
                        {/literal}
                    </script>




            {/if}
            {if $action == "create_ok"}
                <p>You event has been posted. Thank you.</p>
                {$return_block->html_code}
            {/if}
            </div>
            {if $action == "view_event"}
            <center>
                <div class="event_content">
                <h1>Event Details</h1>
                <table align="center" width="500">
                    <tr>
                        <th>Date:</th>
                        <td>{$event_item->date_begin} - {$event_item->date_end} </td>
                    </tr>
                    <!--tr>
                        <th>Time:</th>
                        <td>{$event_item->time_begin} - {$event_item->time_end} </td>
                    </tr-->
                    <tr>
                        <th>Event Name:</th>
                        <td>{$event_item->name}</td>
                    </tr>
                    <tr>
                        <th>Location:</th>
                        <td>{$event_item->location}</td>
                    </tr>
                    <tr>
                        <th>Comments:</th>
                        <td>{$event_item->comments}</td>
                    </tr>
                    </table>
                    <br clear="all">
                    {if !$smarty.get.content_only}
                        <a href="javascript:window.close();">Close Window</a>
                    {/if}
                </div>
            </center>
            {/if}
        </div>
