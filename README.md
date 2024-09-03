Finalizer
=========

What to do when music notation software leaves you stranded.

The company that made Finale went out of business. You now have a few thousand ".mus" and ".musx" scores that you are about to not be able to open anymore. They even had the nerve to tell their users that they would only support new activations for only a year. This meant that if you had to reformat your computer after one year, you wouldn't be able to use Finale anymore. Thankfully, they reverted this decision one day after making it public, but they still haven't published a plan to actually make this a reality. I, for one, don't trust them, so I wrote this software to help those who want a safer option.

# Now what?

You basically have two options:

1. Keep a dedicated computer or virtual machine running Finale 27 (the latest known version) and keep using it as-is. This will work but might not be practical or even possible for everyone.
2. Export all your files into to the [MusicXML](https://www.musicxml.com/) format. This is a generic format that keeps your notes, key changes, etc, but not your fined tune positioning of things (oh, well). This is also a format that can be opened by other notation software packages. Opening these files with another software won't make them look exactly like you had them in Finale, but at least will save you from having to do 90% of the work from scratch.

It's safe to assume that in the long-run, most users will chose option 2 above, and will eventually end up migrating to another notation software. But there's a catch... 

**Exporting each and every one of you scores will take a lifetime!** I wrote this software to make this process as painless and fast as possible, and reducing this process to three steps.

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

Instead of opening and exporting each individual file using Finale, we'll be using the batch export feature. This allows you to point Finale to a specific folder in your computer and convert all the files in there into MusicXML. The problem is that your files are not in one folder! They are scattered around in their respective folders. I mean, who stores all their files in the same place?

This repository contains two CLI commands. Executing the first one will **recursively** search the folder you want and copy all the Finale files it finds into a special `scores` folder. Because the search is "recursive", it will go into all the sub-folders it finds until every single folder under the one you chose is searched in. This is different than Finale's search because Finale's only looks for files in that one folder and ignores sub-folders.

You will now end up with a `scores` folder that contains all your scores. You will notice that the script mangles their filenames a bit. This is because we can't have two files with the same name in the same folder, so to avoid name collisions, the script renames your files. You don't need to worry about this because once we put things back, all the files will be properly renamed to their original names. The whole process shouldn't take more than a couple of seconds assuming a few hundred files. The more files you have, the longer it will take, but it should be fairly quick. You will also note a `scores.json` file. Don't touch this! This is the file that will tell the restoring script where to put the files back.

Now is the time that you will open Finale and do a batch convert of all the files in the `scores` folder (which should be all your Finale files). This can take *a long time*, but you will only have to do this once.

After Finale finished converting all the `scores` folder, you can run the restore script. This will put every converted file algon their Finale counterparts in their original location. The original files are never touched; we just copy them to the temporary `scores` fodler, convert them, and put the converted files back. 

At this point, you can delete the `scores` folder and the `scores.json` files, and go learn a new piece of notation software. You should now have your old Finale files, and the newer MusicXML files alongside them.

# Version history

- 2024-09-02 1.0.0 First release.

# Was this helpful?

If you found this tool valuable, please consider making a PayPal donation by following this link: https://www.paypal.com/donate/?hosted_button_id=MRDHDE7HYYS6E
