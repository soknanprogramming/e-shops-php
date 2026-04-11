import fs from 'fs';
import path from 'path';
import chalk from 'chalk';

interface RestoreConfig {
  sourceDir: string;
  targetDir: string;
  files: string[];
}

const config: RestoreConfig = {
  sourceDir: path.resolve(__dirname, '../uploads/categories/restore_img'),
  targetDir: path.resolve(__dirname, '../uploads/categories/categories'),
  files: ['default.jpg'],
};

function restoreFile(fileName: string): void {
  const sourcePath = path.join(config.sourceDir, fileName);
  const targetPath = path.join(config.targetDir, fileName);

  console.log(chalk.cyan('\nFile Restore Tool'));
  console.log(chalk.gray('─'.repeat(40)));

  // Check if source file exists
  if (!fs.existsSync(sourcePath)) {
    console.error(chalk.red('\nError: Source file not found'));
    console.error(chalk.yellow(`  ${path.relative(path.resolve(__dirname, '..'), sourcePath)}\n`));
    process.exit(1);
  }

  // Check if target file exists
  if (fs.existsSync(targetPath)) {
    console.log(chalk.green('\nTarget file already exists'));
    console.log(chalk.gray(`  ${path.relative(path.resolve(__dirname, '..'), targetPath)}\n`));
    return;
  }

  // Create target directory if it doesn't exist
  if (!fs.existsSync(config.targetDir)) {
    fs.mkdirSync(config.targetDir, { recursive: true });
    console.log(chalk.blue('Created directory: ') + chalk.gray(path.relative(path.resolve(__dirname, '..'), config.targetDir)));
  }

  // Copy file
  try {
    fs.copyFileSync(sourcePath, targetPath);
    console.log(chalk.green('\nSuccessfully restored file'));
    console.log(chalk.gray(`  From: ${path.relative(path.resolve(__dirname, '..'), sourcePath)}`));
    console.log(chalk.gray(`  To:   ${path.relative(path.resolve(__dirname, '..'), targetPath)}\n`));
  } catch (error) {
    console.error(chalk.red('\nError copying file:'));
    console.error(chalk.yellow(`  ${(error as Error).message}\n`));
    process.exit(1);
  }
}

function restoreAll(): void {
  console.log(chalk.cyan('\nFile Restore Tool'));
  console.log(chalk.gray('─'.repeat(40)));

  let restored = 0;
  let skipped = 0;

  for (const fileName of config.files) {
    const sourcePath = path.join(config.sourceDir, fileName);
    const targetPath = path.join(config.targetDir, fileName);

    // Check if source file exists
    if (!fs.existsSync(sourcePath)) {
      console.log(chalk.yellow(`  [skip] ${fileName} (source not found)`));
      skipped++;
      continue;
    }

    // Check if target file exists
    if (fs.existsSync(targetPath)) {
      console.log(chalk.dim(`  [skip] ${fileName} (already exists)`));
      skipped++;
      continue;
    }

    // Create target directory if it doesn't exist
    if (!fs.existsSync(config.targetDir)) {
      fs.mkdirSync(config.targetDir, { recursive: true });
    }

    // Copy file
    try {
      fs.copyFileSync(sourcePath, targetPath);
      console.log(chalk.dim(`  [${restored + 1}]`), chalk.bold(fileName));
      restored++;
    } catch (error) {
      console.error(chalk.red(`\nError copying ${fileName}:`));
      console.error(chalk.yellow(`  ${(error as Error).message}\n`));
    }
  }

  console.log(chalk.green('\nRestore complete'));
  console.log(chalk.bold('Restored:'), chalk.green(restored));
  console.log(chalk.bold('Skipped:'), chalk.yellow(skipped));
  console.log(chalk.bold('Total available:'), chalk.cyan(config.files.length) + '\n');
}

// Main execution
const args = process.argv.slice(2);

if (args.length === 0) {
  // Restore all configured files
  restoreAll();
} else {
  // Restore specific file
  const fileName = args[0];
  restoreFile(fileName);
}
