<div class="pagination" style="width:100%;padding:20px 0 20px 0;">
    <div style="float:left;width:28%">
    </div>
    <div style="float:right;width:70%;">
        <!-- First page link -->
        <?php if (isset($this->previous)): ?>
              <a href="<?php echo $this->url(array('page' => $this->first), null, false, false, false); ?>">Start</a> |
        <?php else: ?>
                <span class="disabled">Start</span> |
        <?php endif; ?>
    
        <!-- Previous page link -->
    
        <?php if (isset($this->previous)): ?>
              <a href="<?= $this->url(array('page' => $this->previous), null, false, false, false); ?>">&lt; Previous</a> |
        <?php else: ?>
            <span class="disabled">&lt; Previous</span> |
        <?php endif; ?>
        <!-- Numbered page links -->
        <?php foreach ($this->pagesInRange as $page): ?>
            <?php if ($page != $this->current): ?>
                <a href="<?= $this->url(array('page' => $page), null, false, false, false); ?>"><?php echo $page; ?></a>
            <?php else: ?>
                <?php echo $page; ?>
            <?php endif; ?>
        <?php endforeach; ?>
        <!-- Next page link -->
        <?php if (isset($this->next)): ?>
              | <a href="<?= $this->url(array('page' => $this->next), null, false, false, false); ?>">Next &gt;</a> |
        <?php else: ?>
            | <span class="disabled">Next &gt;</span> |
        <?php endif; ?>
        <!-- Last page link -->
        <?php if (isset($this->next)): ?>
              <a href="<?php echo $this->url(array('page' => $this->last), null, false, false, false); ?>">End</a>
        <?php else: ?>
            <span class="disabled">End</span>
        <?php endif; ?>
        &nbsp; Page <?= $this->current; ?> of <?= $this->last; ?>
    </div>
 </div>