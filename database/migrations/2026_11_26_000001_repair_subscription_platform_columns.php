<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add the platform columns to `subscriptions` — the part of the platform migration that can
 * fail independently on an existing database (PostgreSQL rejects several ADD COLUMNs in one
 * ALTER, and an older install may already have some of them).
 *
 * Each column is added on its own, so one stubborn column can never stop the rest.
 */
return new class extends Migration
{
    private array $columns = [
        'product' => ['type' => 'string', 'args' => [20], 'default' => 'freight'],
        'sms_quota' => ['type' => 'unsignedInteger', 'args' => [], 'default' => 0],
        'sms_used' => ['type' => 'unsignedInteger', 'args' => [], 'default' => 0],
        'amount' => ['type' => 'decimal', 'args' => [19, 2], 'default' => 0],
        'locked_at' => ['type' => 'timestamp', 'args' => [], 'nullable' => true],
        'notes' => ['type' => 'text', 'args' => [], 'nullable' => true],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('subscriptions')) {
            return;
        }

        foreach ($this->columns as $name => $definition) {
            if (Schema::hasColumn('subscriptions', $name)) {
                continue;
            }

            try {
                Schema::table('subscriptions', function (Blueprint $table) use ($name, $definition) {
                    $type = $definition['type'];
                    $column = $table->{$type}($name, ...$definition['args']);

                    if ($definition['nullable'] ?? false) {
                        $column->nullable();
                    } elseif (isset($definition['default'])) {
                        $column->default($definition['default']);
                    }
                });
            } catch (\Throwable $e) {
                // Report it, but keep going: the other columns still matter.
                report($e);
            }
        }
    }

    public function down(): void
    {
        foreach (array_keys($this->columns) as $name) {
            if (Schema::hasTable('subscriptions') && Schema::hasColumn('subscriptions', $name)) {
                Schema::table('subscriptions', fn (Blueprint $table) => $table->dropColumn($name));
            }
        }
    }
};
