import os
import PIL
from PIL import Image, ImageTk
import tkinter as tk
import time

# get env vars from docker container
path_to_images = os.environ.get('pathtoimages', "/images")
slide_delay_in_seconds = os.environ.get('delayinsecs', 10)
def load_image(filepath):
    """Lazy load an image."""
    return Image.open(filepath)

def start_slideshow(image_folder, delay=3):
    """Start the slideshow from the given folder."""
    # Get a list of image file paths
    image_files = [
        os.path.join(image_folder, file)
        for file in os.listdir(image_folder)
        if file.lower().endswith(('png', 'jpg', 'jpeg', 'gif', 'bmp'))
    ]

    if not image_files:
        print("No images found in the folder.")
        return

    # Initialize the Tkinter window
    root = tk.Tk()
    root.attributes('-fullscreen', True)  # Fullscreen mode
    root.configure(background='black')

    # Label to display the images
    label = tk.Label(root, bg='black')
    label.pack(expand=True)

    def show_image(index):
        """Display the image at the current index."""
        image_path = image_files[index % len(image_files)]  # Loop through images
        image = load_image(image_path)

        # Resize the image to fit the screen
        screen_width = root.winfo_screenwidth()
        screen_height = root.winfo_screenheight()
        image.thumbnail((screen_width, screen_height), Image.LANCZOS)

        # Convert the image for Tkinter
        tk_image = ImageTk.PhotoImage(image)
        label.config(image=tk_image)
        label.image = tk_image

        # Schedule the next image
        root.after(delay * 1000, lambda: show_image(index + 1))

    # Start the slideshow
    show_image(0)

    # Exit on any key press
    root.bind("<Escape>", lambda e: root.destroy())
    root.bind("<KeyPress>", lambda e: root.destroy())

    root.mainloop()

if __name__ == "__main__":
    # Path to the folder containing images
    image_folder = path_to_images

    # Time in seconds for each slide
    delay = slide_delay_in_seconds

    start_slideshow(image_folder, delay)
