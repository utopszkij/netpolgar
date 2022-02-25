<x-guest-layout>  
	<div id="eventContainer">
    <div class="row" style="margin-top: 5rem;">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>
                    <a href="/{{ $parentType }}/{{ $parent->id }}">
                    	<em class="fas fa-hand-point-right"></em>{{ $parent->name}}
                    </a>
                </h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
        	<h2>{{ __('event.list') }}</h2>
		</div>
	</div>    
	@if ($userAdmin) 
    <div class="row">
        <div class="col-lg-12 margin-tb">
        	<a class="btn btn-primary" 
        		href="{{ \URL::to('/'.$parentType.'/'.$parentId.'/events/create') }}">
        		<em class="fas fa-plus-circle"></em>{{ __('event.add') }}
        	</a>
		</div>
	</div>
	@endif


    <table class="table table-bordered">
    	<thead>
        <tr>
            <th>{{ __('event.id') }}</th>
            <th>{{ __('event.name') }}</th>
            <th>{{ __('event.description') }}</th>
            <th>{{ __('event.date') }}</th>
            <th>{{ __('event.location') }}</th>
        </tr>
      </thead>
      <tbody>  
        @foreach ($data as $key => $value)
        <tr>
            <td>{{ $value->id }}</td>
            <td>
            	<a href="{{ \URL::to('/files/'.$value->id) }}">
            		{{ $value->name }}
            	</a>
            </td>
            <td>{{ mb_substr($value->description,0,60) }}</td>
            <td>{{ $value->date }}</td>
            <td>{{ $value->location }}</td>
        </tr>
        @endforeach
       </tbody> 
    </table>
    @if (count($data) > 0)
    <p class="help">További részletekért, letöltéshez kattints a névre!</p>
    @else 
    <p>{{ __('event.notData') }}</p>
    @endif
    {!! $data->links() !!}
  </div>        
</x-guest-layout>  
