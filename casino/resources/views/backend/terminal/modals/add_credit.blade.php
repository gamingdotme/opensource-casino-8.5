<div class="modal fade cusModel success" id="addCredit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">

        <div class="modal-content text-center">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Balance Add
                </h5>
            </div>
            <form action="{{url('backend/terminal/balance/add')}}" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{$response['terminal']->id}}">
                <div class="modal-body">
                    <table class="table vm table-bordered">
                        <tr>
                            <td class="w200">
                                Amount
                                <p><span class="hint">Enter the amount you want to add.</span></p>
                            </td>
                            <td class="text-left"><input type="text" required name="amount" class="form-control fs-20 w250">

                            </td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>