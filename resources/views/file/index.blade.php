<x-guest-layout>  
	<div id="fileContainer">
    <div class="helpBtn">
			<a href="#" onclick="help('files')">
				<em class="fas fa-book"></em>Súgó
			</a>	
	</div>
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
        	<h2>{{ __('file.list') }}</h2>
		</div>
	</div>    
	@if (($userAdmin) | ($userMember)) 
    <div class="row">
        <div class="col-lg-12 margin-tb">
        	<a class="btn btn-primary" 
        		href="{{ \URL::to('/'.$parentType.'/'.$parentId.'/files/create') }}">
        		<em class="fas fa-plus-circle"></em>{{ __('file.add') }}
        	</a>
		</div>
	</div>
	@endif


    <table class="table table-bordered">
    	<thead>
        <tr>
            <th>{{ __('file.id') }}</th>
            <th>{{ __('file.name') }}</th>
            <th>{{ __('file.description') }}</th>
            <th>{{ __('file.type') }}</th>
            <th>{{ __('file.licence') }}</th>
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
            <td>{{ $value->type }}</td>
            <td>{{ $value->licence }}</td>
        </tr>
        @endforeach
       </tbody> 
    </table>
    @if (count($data) > 0)
    <p class="help">További részletekért, letöltéshez kattints a névre!</p>
    @else 
    <p>{{ __('file.notData') }}</p>
    @endif
    {!! $data->links() !!}
  </div>        
</x-guest-layout>  
