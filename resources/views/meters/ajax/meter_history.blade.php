
<strong>History</strong><br />

<table class="table10 table-bordered  table-hover">

    @forelse ($meter_info->histories as $history)
        <tr>
            <td>{{ @date('m/d/Y',strtotime($history->created_at))  }}</td>
            <td class="rel1">
                <p>

                        {{ @$history->account->acct_no.' - '.@$history->account->fname.' '.@$history->account->lname  }}
                        <br />
                        {{ @$history->account->address1 }}
                        <br />
                        {{ @$history->typ  }}  ON : {{ date('m/d/Y', strtotime(@$history->served_date)) }}
                        <br />
                        BY: {{ @$history->served_name }} 
                        <br />

                        @if ( !empty($history->remaks) )
                            REMARKS : 
                            <br /> 
                            {{$history->remaks}}
                        @endif
                </p>
                <div class="but-right">
                    <span class="edit-but red" onclick="add_edit_remarks_pop('<?php echo $loop->index; ?>')">+ Remarks</span>
                </div>
            </td>
        </tr>


    @empty
        <tr>
            <td colspan="2">
                No History
            </td>
        </tr>
    @endforelse


</table>