<!-- goals.tpl -->
<div class="row">
    <div id='goal_cards_container' class="col-12 mt-3 mb-2">
        <div class="card">
            <div class="card-body">
                <div class="card-header-heading">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="row pg_heading_line_ht">Goal Cards</h4>
                        </div>
                    </div>
                </div>

                <?php if (isset($message)): ?>
                    <div class="alert alert-success mt-2" role="alert">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger mt-2" role="alert">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <div class="years-list mb-4">
                    <?php foreach ($years as $year): ?>
                        <a href="?year=<?= $year ?>" 
                           class="btn <?= $year === $selectedYear ? 'btn-primary' : 'btn-secondary' ?> me-2">
                            <?= htmlspecialchars($year) ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <form method="POST" action="">
                    <input type="hidden" name="year" value="<?= htmlspecialchars($selectedYear) ?>">
                    
                    <!-- Desktop View -->
                    <div class="d-none d-lg-block">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <?php foreach ($columns as $label): ?>
                                        <th><?= htmlspecialchars($label) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($this->body_template_data['categories'] as $key => $label):
                                $goalCard=$this->body_template_data['goalCard'];
                                //print_r($goalCard); exit;
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($label) ?></td>
                                    <td><textarea name="<?= $key ?>_goal" class="form-control" rows="4"><?= htmlspecialchars($goalCard[$key]['goal'] ?? '') ?></textarea></td>
                                    <td><textarea name="<?= $key ?>_significance" class="form-control" rows="4"><?= htmlspecialchars($goalCard[$key]['significance'] ?? '') ?></textarea></td>
                                    <td><textarea name="<?= $key ?>_action" class="form-control" rows="4"><?= htmlspecialchars($goalCard[$key]['action_planned'] ?? '') ?></textarea></td>
                                    <td><textarea name="<?= $key ?>_mid" class="form-control" rows="4"><?= htmlspecialchars($goalCard[$key]['mid_review'] ?? '') ?></textarea></td>
                                    <td><textarea name="<?= $key ?>_final" class="form-control" rows="4"><?= htmlspecialchars($goalCard[$key]['final_review'] ?? '') ?></textarea></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile View -->
                    <div class="d-lg-none">
                        <?php foreach ($categories as $key => $label): ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><?= htmlspecialchars($label) ?></h5>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($columns as $column => $columnLabel): ?>
                                        <div class="mb-3">
                                            <label class="form-label"><?= htmlspecialchars($columnLabel) ?></label>
                                            <textarea name="<?= $key ?>_<?= $column ?>" 
                                                      class="form-control" 
                                                      rows="4"><?= htmlspecialchars($goalCard[$key][$column] ?? '') ?></textarea>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="save" class="btn btn-success">
                            <img src="images/check.png" class="check-button" alt="Check">
                            <span>Save Goal Card</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>