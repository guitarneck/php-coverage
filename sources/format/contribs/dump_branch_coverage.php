<?php
/*
--------------------------------------------------------------------
                 The Xdebug License, version 1.02
             (Based on "The PHP License", version 3.0)
   Copyright (c) 2003-2018 Derick Rethans. All rights reserved.
--------------------------------------------------------------------
*/

function dump_branch_coverage($info)
{
	foreach ( $info as $fname => $file )
	{
		if ( preg_match( '/dump-branch-coverage.inc$/', $fname ) )
		{
			continue;
		}
		if ( preg_match( '/dump-branch-coverage.php$/', $fname ) )
		{
			continue;
		}

		if ( !isset( $file['functions'] ) )
		{
			continue;
		}

		ksort( $file['functions'] );
		foreach ( $file['functions'] as $fname => $function )
		{

			if ( $fname == 'dump-branch-coverage' )
			{
				continue;
			}

			echo $fname, "\n", "- branches\n";
			foreach ( $function['branches'] as $bnr => $branch )
			{
				printf( "  - %02d; OP: %02d-%02d; line: %02d-%02d %3s",
					$bnr,
					$branch['op_start'], $branch['op_end'],
					$branch['line_start'], $branch['line_end'],
					$branch['hit'] ? "HIT" : " X "
				);
				foreach ( $branch['out'] as $key => $out )
				{
					if ( $out == 2147483645 )
					{
						printf("; out%d: EX %3s", $key + 1,
							$branch['out_hit'][$key] ? "HIT" : " X "
						);
					}
					else
					{
						printf("; out%d: %02d %3s", $key + 1,
							$branch['out'][$key],
							$branch['out_hit'][$key] ? "HIT" : " X "
						);
					}
				}
				echo "\n";
			}

			echo "- paths\n";
			foreach( $function['paths'] as $path )
			{
				echo '  - ', join( " ", $path['path'] ), ': ';
				echo $path['hit'] ? "HIT\n" : " X \n";
			}
			echo "\n";
		}
	}
}