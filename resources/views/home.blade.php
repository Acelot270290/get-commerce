<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Categorias</h1>
        <div class="row mt-3">
            @foreach($categories as $key => $value)
                <div class="col-md-4 mb-3">
                    <a href="{{ route('category.show', $key) }}" class="btn btn-primary btn-block">
                        <h2>{{ $value }}</h2>
                    </a>
                </div>
            @endforeach
        </div>
        <button id="scrape-button" class="btn btn-success">Adicionar/Atualizar Produtos</button>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#scrape-button').on('click', function() {
                Swal.fire({
                    title: 'Deseja atualizar?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: `Sim`,
                    cancelButtonText: `Não`
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Atualizando...',
                            html: 'Aguarde enquanto os produtos são atualizados.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading()
                            }
                        });

                        $.ajax({
                            url: '{{ route("scrape.products") }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Produtos atualizados com sucesso!',
                                    icon: 'success'
                                });
                            },
                            error: function(xhr, status, error) {
                                Swal.fire({
                                    title: 'Erro ao atualizar produtos!',
                                    text: 'Tente novamente mais tarde.',
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
