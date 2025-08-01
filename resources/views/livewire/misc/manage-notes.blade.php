<div>
    <div class="card">
        <div class="card-header">
            <i class="fa fa-sticky-note"></i> Manage Generic Notes
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th><i class="fa fa-tag"></i> Note Type</th>
                        <th width="70%"><i class="fa fa-sticky-note"></i> Note</th>
                        <th><i class="fa fa-pencil"></i> Edit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notes as $note)
                    <tr>
                        <td>{{ $note->ntitle }}</td>
                        <td>{!! nl2br(e($note->ntext)) !!}</td>
                        <td>
                            <a href="{{ route('misc.manage-notes.edit', ['ntype' => $note->ntype]) }}"
                                class="btn btn-sm btn-primary">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">No notes found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $notes->links() }}
            </div>
        </div>
    </div>
</div>