<html>
      <body>
         <form method='POST'>
            @if($errors->any())
            <ul>
            @foreach($errors->all() as $error)
            <li><strong>{{ $error }}</strong></li>
            @endforeach
            <ul>
            @endif
        @if(session('success'))
            <strong>{{ session('success') }}</strong>
        @endif
            <label for="to">Comma Separated Numbers</label>
            <input type='text' name='to' />
            <br>
            <label>Message/Body</label>
            <textarea name='messages'></textarea>
            <br><br>
            <button type='submit'>Send</button>
            @csrf
      </form>
    </body>
</html>