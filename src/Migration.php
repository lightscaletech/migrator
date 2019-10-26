<?php

namespace Lightscale\Migrator;

interface Migration {
    public function up($db);
    public function down($db);
}
