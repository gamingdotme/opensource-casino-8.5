<div class="element-box-tp">
    <div class="activity-boxes-w">
        <div class="activity-box-w">
            <div class="activity-time">{{ $stat->created_at->diffForHumans() }}</div>
            <div class="activity-box">
                <div class="activity-info">
                    <div class="activity-role"><strong>{{ $stat->user->shop->name }}</strong></div>
                    @if($stat->type=="add")
                    <strong class="activity-title text-primary">In: {{$stat->summ}} Player: {{$stat->user->username}} Bonus: 20%</strong>
                    @else
                    <strong class="activity-title text-danger">Out: {{$stat->summ}} Player: {{$stat->user->username}} </strong>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>


