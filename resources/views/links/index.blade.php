<x-app.layouts title="Add Link">

    <form action="{{ route('links.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="flex flex-col gap-4">
            <div class="flex flex-col gap-2">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" placeholder="Title" required>
            </div>
            <div class="flex flex-col gap-2">
                <label for="description">Description</label>
                <textarea name="description" id="description" placeholder="Description" required></textarea>
            </div>
            <div class="flex flex-col gap-2">
                <label for="url">URL</label>
                <input type="text" name="url" id="url" placeholder="URL" required>
            </div>
            <div class="flex flex-col gap-2">
                <label for="category">Category</label>
                <select name="category" id="category" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}">{{ $category->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button type="submit">Add Link</button>
    </form>


    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

</x-app.layouts>
