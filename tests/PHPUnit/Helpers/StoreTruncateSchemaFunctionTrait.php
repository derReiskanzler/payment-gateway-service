<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Helpers;

use Illuminate\Database\Connection;

trait StoreTruncateSchemaFunctionTrait
{
    private static function storeTruncateSchemaFunction(Connection $connection): void
    {
        $connection->getPdo()->exec(<<<'TRUNC'
CREATE OR REPLACE FUNCTION truncate_schema(_schema character varying)
  RETURNS void AS
$BODY$
declare
    selectrow record;
begin
for selectrow in
select 'TRUNCATE TABLE ' || quote_ident(_schema) || '.' ||quote_ident(t.table_name) || ' CASCADE;' as qry 
from (
     SELECT table_name 
     FROM information_schema.tables
     WHERE table_type = 'BASE TABLE' AND table_schema = _schema AND table_name != 'event_streams' AND table_name != 'migrations'
     )t
loop
execute selectrow.qry;
end loop;
end;
$BODY$
  LANGUAGE plpgsql
TRUNC
        );
    }
}
