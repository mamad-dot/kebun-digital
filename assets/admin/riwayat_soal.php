<?php
session_start();
require_once('../includes/config.php');

if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Riwayat Soal - Admin Panel</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .jawaban-benar {
            font-weight: bold;
            color: #28a745;
        }

        .pilihan-benar {
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Admin Panel</a>
            <div class="ml-auto">
                <div class="ml-auto">
                    <a href="dashboard.php" class="btn btn-light mr-2"><i class="fas fa-home"></i> Dashboard</a>
                    <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-history"></i> Riwayat Soal</h5>
                        <div class="d-flex align-items-center">
                            <select class="form-control" id="filterKategori" name="kategori">
                                <option value="">Semua Kategori</option>
                                <option value="basic">Basic</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                        </div>
                        <button id="btnHapusSemua" class="btn btn-danger mr-2"><i class="fas fa-trash"></i> Hapus Semua
                            Soal</button>
                        <a href="import_soal.php" class="btn btn-primary"><i class="fas fa-file-import"></i> Import
                            Soal</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Pertanyaan</th>
                                        <th>Pilihan A</th>
                                        <th>Pilihan B</th>
                                        <th>Pilihan C</th>
                                        <th>Pilihan D</th>
                                        <th width="100">Jawaban</th>
                                        <th width="100">Kategori</th>
                                        <th width="150">Tanggal Import</th>
                                        <th width="150">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $where = "";
                                    if (isset($_GET['kategori']) && !empty($_GET['kategori'])) {
                                        $kategori = mysqli_real_escape_string($conn, $_GET['kategori']);
                                        $where = " WHERE kategori = '$kategori'";
                                    }

                                    $query = "SELECT * FROM soal $where ORDER BY created_at DESC";
                                    $result = mysqli_query($conn, $query);
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $pilihan_benar = '';
                                        switch ($row['jawaban_benar']) {
                                            case 'A':
                                                $pilihan_benar = $row['pilihan_a'];
                                                break;
                                            case 'B':
                                                $pilihan_benar = $row['pilihan_b'];
                                                break;
                                            case 'C':
                                                $pilihan_benar = $row['pilihan_c'];
                                                break;
                                            case 'D':
                                                $pilihan_benar = $row['pilihan_d'];
                                                break;
                                        }
                                    ?>
                                        <tr>
                                            <td class="text-center"><?php echo $no++; ?></td>
                                            <td><?php echo htmlspecialchars($row['pertanyaan']); ?></td>
                                            <td
                                                <?php echo ($row['jawaban_benar'] == 'A') ? 'class="pilihan-benar"' : ''; ?>>
                                                <?php echo htmlspecialchars($row['pilihan_a']); ?>
                                            </td>
                                            <td
                                                <?php echo ($row['jawaban_benar'] == 'B') ? 'class="pilihan-benar"' : ''; ?>>
                                                <?php echo htmlspecialchars($row['pilihan_b']); ?>
                                            </td>
                                            <td
                                                <?php echo ($row['jawaban_benar'] == 'C') ? 'class="pilihan-benar"' : ''; ?>>
                                                <?php echo htmlspecialchars($row['pilihan_c']); ?>
                                            </td>
                                            <td
                                                <?php echo ($row['jawaban_benar'] == 'D') ? 'class="pilihan-benar"' : ''; ?>>
                                                <?php echo htmlspecialchars($row['pilihan_d']); ?>
                                            </td>
                                            <td class="text-center jawaban-benar"><?php echo $row['jawaban_benar']; ?></td>
                                            <td class="text-center"><?php echo ucfirst($row['kategori']); ?></td>
                                            <td class="text-center">
                                                <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-info edit-soal"
                                                    data-id="<?php echo $row['id']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger hapus-soal"
                                                    data-id="<?php echo $row['id']; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Soal -->
    <div class="modal fade" id="editSoalModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Soal</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="formEditSoal">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editId">
                        <div class="form-group">
                            <label>Kategori</label>
                            <select class="form-control" name="kategori" required>
                                <option value="basic">Basic</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Pertanyaan</label>
                            <textarea class="form-control" name="pertanyaan" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Pilihan A</label>
                            <input type="text" class="form-control" name="pilihan_a" required>
                        </div>
                        <div class="form-group">
                            <label>Pilihan B</label>
                            <input type="text" class="form-control" name="pilihan_b" required>
                        </div>
                        <div class="form-group">
                            <label>Pilihan C</label>
                            <input type="text" class="form-control" name="pilihan_c" required>
                        </div>
                        <div class="form-group">
                            <label>Pilihan D</label>
                            <input type="text" class="form-control" name="pilihan_d" required>
                        </div>
                        <div class="form-group">
                            <label>Jawaban Benar</label>
                            <select class="form-control" name="jawaban_benar" required>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Filter kategori
            $('#filterKategori').change(function() {
                window.location.href = 'riwayat_soal.php?kategori=' + $(this).val();
            });

            // Edit soal
            $('.edit-soal').click(function() {
                var id = $(this).data('id');
                // Ambil data soal dengan AJAX
                $.get('get_soal.php?id=' + id, function(data) {
                    var soal = JSON.parse(data);
                    $('#editId').val(soal.id);
                    $('#formEditSoal [name=kategori]').val(soal.kategori);
                    $('#formEditSoal [name=pertanyaan]').val(soal.pertanyaan);
                    $('#formEditSoal [name=pilihan_a]').val(soal.pilihan_a);
                    $('#formEditSoal [name=pilihan_b]').val(soal.pilihan_b);
                    $('#formEditSoal [name=pilihan_c]').val(soal.pilihan_c);
                    $('#formEditSoal [name=pilihan_d]').val(soal.pilihan_d);
                    $('#formEditSoal [name=jawaban_benar]').val(soal.jawaban_benar);
                    $('#editSoalModal').modal('show');
                });
            });

            // Hapus soal
            $('.hapus-soal').click(function() {
                if (confirm('Apakah Anda yakin ingin menghapus soal ini?')) {
                    var id = $(this).data('id');
                    window.location.href = 'hapus_soal.php?id=' + id;
                }
            });

            // Hapus semua soal
            $('#btnHapusSemua').click(function() {
                if (confirm('Apakah Anda yakin ingin menghapus SEMUA soal?')) {
                    window.location.href = 'hapus_soal.php?all=1';
                }
            });
        });
    </script>
</body>

</html>