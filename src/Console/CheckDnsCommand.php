<?php

namespace SocialDept\AtpSchema\Console;

use Illuminate\Console\Command;
use SocialDept\AtpSchema\Authoring\LexiconFiles;
use SocialDept\AtpSchema\Authoring\NetworkLexicons;
use SocialDept\AtpSupport\Nsid;

class CheckDnsCommand extends Command
{
    protected $signature = 'schema:check-dns
                            {nsid?* : Lexicons to check, defaults to every local file}
                            {--did= : The DID that should publish them}';

    protected $description = 'Check the _lexicon DNS records that let others find your lexicons';

    public function handle(NetworkLexicons $network): int
    {
        $targets = $this->argument('nsid') ?: array_keys(LexiconFiles::fromConfig()->all());
        $expected = $this->option('did');
        $names = [];

        // One TXT record covers every NSID under the same authority, and
        // nothing is inherited from a parent authority.
        foreach ($targets as $nsid) {
            if (Nsid::isValid($nsid)) {
                $names[NetworkLexicons::dnsName($nsid)][] = $nsid;
            }
        }

        ksort($names);
        $missing = [];
        $failed = false;

        foreach ($names as $name => $nsids) {
            $did = $network->authority($nsids[0]);

            if ($did === null) {
                $missing[] = $name;
                $this->components->twoColumnDetail($name, '<fg=red>missing</>');
            } elseif (is_string($expected) && $did !== $expected) {
                $failed = true;
                $this->components->twoColumnDetail($name, "<fg=red>names {$did}, not {$expected}</>");
            } else {
                $this->components->twoColumnDetail($name, "<fg=green>{$did}</>");
            }
        }

        if ($missing !== []) {
            $this->newLine();
            $this->line('  Add these TXT records:');

            foreach ($missing as $name) {
                $this->line("    {$name}  TXT  \"did=".(is_string($expected) ? $expected : '<your DID>').'"');
            }
        }

        return $missing !== [] || $failed ? self::FAILURE : self::SUCCESS;
    }
}
