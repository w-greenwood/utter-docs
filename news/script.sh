POSITIONAL_ARGS=()

while [[ $# -gt 0 ]]; do
  case $1 in
    -i|--in-ident)
      IN_IDENT="$2"
      shift # past argument
      shift # past value
      ;;
    -b|--bed)
      BED="$2"
      shift # past argument
      shift # past value
      ;;
    -o|--out-ident)
      OUT_IDENT="$2"
      shift # past argument
      shift # past value
      ;;
    -*|--*)
      echo "Unknown option $1"
      exit 1
      ;;
    *)
      POSITIONAL_ARGS+=("$1") # save positional arg
      shift # past argument
      ;;
  esac
done

sox assets/upload.wav -c 1 -r 48k assets/temp.wav \
norm -3 \
silence -l 1 0.1 1% -1 2.0 1% \
compand 0.01,1 -90,-90,-70,-70,-60,-20,0,0 -5

fadein=$((48000 * 1))
fadeout=$((48000 * 4))
padding=$((fadein + fadeout))

length=$(soxi -s assets/temp.wav)
length=$((length + padding))

sox assets/temp.wav assets/vox.wav pad "$fadein"s 0
sox assets/"$BED".wav assets/temp.wav \
trim 0s "$length"s \
fade 0 -0 "$fadeout"s

sox -c 2 assets/"$IN_IDENT".wav "|sox -M assets/vox.wav assets/temp.wav -c 1 -t sox -"  -c 2 assets/"$OUT_IDENT".wav assets/news.mp3 norm -3

exit 0
