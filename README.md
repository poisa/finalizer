Finalizer
=========

What to do when music notation software leaves you stranded.

The company that created Finale has gone out of business. Now, you have a few thousand “.mus” and “.musx” scores that you may soon be unable to open. Initially, they announced that they would only support new activations for one year, meaning that if you needed to reformat your computer after that time, you wouldn’t be able to use Finale anymore. Fortunately, they reversed this decision a day after making it public. However, they still haven’t provided a concrete plan to implement this change. Personally, I don’t trust them, so I developed this software to offer users a safer alternative.

# Now what?

You basically have two options:

1. Keep a dedicated computer or virtual machine running Finale 27 (the latest known version) and keep using it as-is. This approach works but might not be practical or even possible for everyone.
2. Export all your files to the [MusicXML](https://www.musicxml.com/) format. This is a generic format that preserves your notes, key changes, etc., but not the fine-tuned positioning of elements (oh, well). MusicXML can be opened by other notation software. While these files won’t look exactly as they did in Finale, at least you’ll avoid having to redo 90% of the work from scratch.

It's safe to assume that in the long-run, most users will chose option 2 above, and will eventually end up migrating to another notation software. But there's a catch... 

**Exporting each and every one of your scores would take forever!** I wrote this software to make the process as painless and fast as possible, reducing it to just three steps.

# Requirements

* Finale v27. I'm not sure versions previous to 27 can do the batch convert but if you've taken advantage of their Dorico cross-grade option, you'll be able to download 27 as well.  
* PHP version 8.0 or higher. If you're using a Mac, this is already there, or you can install it using [homebrew](https://brew.sh/).
* MacOS. You *could* run this on Windows but you'll need to be a bit more tech-savvy in order to translate the instructions.

# How to use

If you're a non-technical user and want to understand how this works, please skip this section and read [the non-technical explanation](#non-technical-explanation-of-how-this-works).

### Overview:

1. Run `find.php` to capture all your Finale files into a temporary folder (managed by the script).
2. Perform a Batch export in Finale on this folder.
3. Run `restore.php` to put back the converted files into their original folders.

### Step by step

1. Download this software. Either clone the repo (if you know what this means), or hit the green "Code" button above, and then Download ZIP. Unzip the file.
2. Open up a Terminal window in the directory you unzipped the code.
3. Run

```bash
php find.php ~/Documents
```

This will recursively find all your Finale files in `~/Documents` (which is the user's Document folder). Feel free to change this patch into any path you want, though `~/Documents` should work for most users. If you have files in an external drive, you could so something like:

```bash
php find.php /Volumes/MyScores
```

Once all the files have been found, continue on to the next step.

4. Open up Finale, and go to `File > Export > Translate Folder to MusicXML`. It will open up a window prompting you to chose a folder. Chose the one where you unzipped the file in step one, and then find the newly created `scores` folder. All your scores should be there. Don't worry if the filenames look odd; we'll fix them later. Finale could take an eternity or more depending on how many files you have. 
5. We need to put everything back where it was. Run:

```bash
php restore.php
```

You're done. Feel free to remove the `scores` folder and the `scores.json` file that were created in step 3.

If something doesn't work as expected, or you want more control over the last step, there are a couple of flags you can pass to the restore script to help you further:

### Interactive restores

If the destination file already exists, this will ask you before copying anything over. Answering yes will overwrite the destination file.

```bash
php restore.php --interactive
```

### Forceful restores

This will replace any existing files without asking.

```bash
php restore.php --force
```

### IMPORTANT: Your original files are never touched by these scripts!

No Finale files are ever modified or deleted. The only thing this software does is copy your scores over to a safe location, you convert the files using Finale, and then it puts the converted files in the same folder as the original Files. Then again, user error can happen so use these scripts at your own risk.

### Are you sure this will not delete my files?

I am. Still, you shouldn't be executing code you got off the internet willy-nilly. If you do, and bad things happen, you're the only one to blame. This code is provided for free with no guarantees. 

Go and paste the contents of these files into ChatGPT or your preferred AI, and ask it questions. "Is this safe?", "what does this do?", etc. Better yet, ask your developer friend to look them over. This is a good practice to keep your data safe. You might also learn something, which is a good thing.

After all, you already have multiple backups of everything right?

# Non-technical explanation of how this works

Instead of opening and exporting each individual file using Finale, we’ll use the batch export feature. This feature lets you point Finale to a specific folder on your computer and convert all the files in that folder to MusicXML. The problem is, your files aren’t all in one place—they’re scattered across various folders. After all, who keeps all their files in the same folder?

This repository contains two CLI commands to help with this. The first command will **recursively** search through the folder you choose and copy all the Finale files it finds into a special `scores` folder. Because the search is “recursive,” it will go through all the sub-folders, ensuring that every folder under the one you selected is searched. This is different from Finale’s search, which only looks for files in a single folder and ignores sub-folders.

You might notice that the script changes some of the filenames. This is to prevent duplicates, as you can’t have two files with the same name in the same folder. Don’t worry about this; once we move the files back, they’ll be renamed to their original names. The whole process should only take a few seconds if you have a few hundred files. If you have more files, it might take a bit longer, but it should still be relatively quick. You’ll also see a `scores.json` file—don’t touch this! It’s important for the restore script, as it tells the script where to return the files.

Now, will now need to open Finale and do a batch conversion of all the files in the `scores` folder (which should now contain all your Finale files). This conversion process might take _a while_, but you’ll only need to do it once.

After Finale finishes converting the files in the scores folder, you can run the restore script. This will place each converted file alongside its original Finale counterpart in the original location. **The original files are never modified; they’re just copied to the temporary scores folder, converted, and then the converted files are moved back.**

At this point, you can delete the `scores` folder and the `scores.json` file. Now, you should have both your old Finale files and the new MusicXML files stored alongside them, ready for use with your new notation software.

# Version history

- 2025-02-10 1.0.1 Add support for uppercase filenames. Thanks to [ScottOaks](https://github.com/ScottOaks)!
- 2024-09-02 1.0.0 First release.

# Was this helpful?

If you found this tool valuable, please consider making a PayPal donation by following this link: https://www.paypal.com/donate/?hosted_button_id=MRDHDE7HYYS6E
